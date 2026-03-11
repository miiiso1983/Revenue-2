<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\MonthlyAllocation;
use App\Models\Installment;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display pivot table report
     */
    public function index(Request $request)
    {
        ['period' => $period, 'startDate' => $startDate, 'endDate' => $endDate] = $this->resolveDateFilters($request);

        $currency = $request->input('currency', '');
        $clientFilter = $request->input('client', '');
        $appFilter = $request->input('app_name', '');
        $dataType = $request->input('data_type', 'both'); // both, revenue, installments

        // Get unique app names for filter dropdown
        $appNames = Contract::select('app_name')
            ->distinct()
            ->whereNotNull('app_name')
            ->where('app_name', '!=', '')
            ->orderBy('app_name')
            ->pluck('app_name');

        // Generate pivot data
        $pivotData = $this->generatePivotData($startDate, $endDate, $currency, $clientFilter, $appFilter);

        return view('reports.pivot', compact(
            'pivotData',
            'period',
            'startDate',
            'endDate',
            'currency',
            'clientFilter',
            'appFilter',
            'dataType',
            'appNames'
        ));
    }

    /**
     * Generate pivot table data
     */
    private function generatePivotData($startDate, $endDate, $currency = '', $clientFilter = '', $appFilter = '')
    {
        $start = Carbon::parse("{$startDate}-01")->startOfMonth();
        $endMonth = Carbon::parse("{$endDate}-01")->startOfMonth();
        $rangeEnd = $endMonth->copy()->endOfMonth();
        
        // Generate month columns
        $months = [];
        $current = $start->copy();
        while ($current <= $endMonth) {
            $months[] = $current->format('Y-m-d');
            $current->addMonth();
        }

        // Initialize monthly totals (across all clients)
        $monthTotals = [];
        foreach ($months as $month) {
            $monthTotals[$month] = [
                'by_currency' => [], // e.g. ['USD' => ['revenue' => ..., 'installments' => ..., 'discount' => ...]]
                'all' => [
                    'revenue'      => 0,
                    'installments' => 0,
                    'discount'     => 0,
                ],
            ];
        }
        
        // Get contracts with filters
        $contractsQuery = Contract::query()
            ->with([
                'monthlyAllocations' => fn ($query) => $query
                    ->whereBetween('month_date', [$start->toDateString(), $rangeEnd->toDateString()])
                    ->orderBy('month_date'),
                'installments' => fn ($query) => $query
                    ->whereBetween('due_date', [$start->toDateString(), $rangeEnd->toDateString()])
                    ->orderBy('due_date'),
            ])
            ->whereDate('invoice_date', '<=', $rangeEnd->toDateString());
        
        if ($currency) {
            $contractsQuery->where('currency', $currency);
        }
        
        if ($clientFilter) {
            $contractsQuery->where('client_name', 'like', "%{$clientFilter}%");
        }
        
        if ($appFilter) {
            $contractsQuery->where('app_name', 'like', "%{$appFilter}%");
        }
        
        $contracts = $contractsQuery
            ->orderBy('client_name')
            ->orderBy('invoice_date')
            ->orderBy('invoice_number')
            ->get()
            ->filter(fn (Contract $contract) => $this->contractOverlapsRange($contract, $start, $endMonth))
            ->values();
        
        // Build one row per contract/invoice to avoid merging separate contract periods
        $clientData = [];
        
        foreach ($contracts as $contract) {
            $rowKey = (string) $contract->id;
            $clientName = $contract->client_name;
            $contractCurrency = $contract->currency;
            
            $clientData[$rowKey] = [
                    'client_name' => $clientName,
                    'invoices' => [$contract->invoice_number],
                    'months' => [],
                ];

            $allocations = $contract->monthlyAllocations
                ->groupBy(fn (MonthlyAllocation $item) => Carbon::parse($item->month_date)->startOfDay()->format('Y-m-d'))
                ->map(fn ($items) => [
                    'revenue' => (float) $items->sum(fn (MonthlyAllocation $item) => (float) $item->allocated_amount),
                    'discount' => (float) $items->sum(fn (MonthlyAllocation $item) => (float) $item->discount_amount),
                ]);

            $installments = $contract->installments
                ->groupBy(fn (Installment $item) => Carbon::parse($item->due_date)->startOfDay()->format('Y-m-d'))
                ->map(fn ($items) => (float) $items->sum(fn (Installment $item) => (float) $item->installment_amount));
            
            // Populate month data and accumulate totals
            foreach ($months as $month) {
                $clientData[$rowKey]['months'][$month] = [
                        'revenue' => 0,
                        'installments' => 0,
                        'discount' => 0,
                        'currency' => $contract->currency,
                    ];

                $allocationValues = $allocations->get($month, ['revenue' => 0, 'discount' => 0]);
                $allocatedAmount = (float) $allocationValues['revenue'];
                $discountAmount = (float) $allocationValues['discount'];
                $installmentAmount = (float) $installments->get($month, 0);

                // Add revenue & discount
                if ($allocatedAmount > 0 || $discountAmount > 0 || $installmentAmount > 0) {
                    if (!isset($monthTotals[$month]['by_currency'][$contractCurrency])) {
                        $monthTotals[$month]['by_currency'][$contractCurrency] = [
                            'revenue'      => 0,
                            'installments' => 0,
                            'discount'     => 0,
                        ];
                    }

                    $clientData[$rowKey]['months'][$month]['revenue']  += $allocatedAmount;
                    $clientData[$rowKey]['months'][$month]['discount'] += $discountAmount;
                    $clientData[$rowKey]['months'][$month]['installments'] += $installmentAmount;

                    $monthTotals[$month]['by_currency'][$contractCurrency]['revenue']  += $allocatedAmount;
                    $monthTotals[$month]['by_currency'][$contractCurrency]['discount'] += $discountAmount;
                    $monthTotals[$month]['by_currency'][$contractCurrency]['installments'] += $installmentAmount;

                    $monthTotals[$month]['all']['revenue']  += $allocatedAmount;
                    $monthTotals[$month]['all']['discount'] += $discountAmount;
                    $monthTotals[$month]['all']['installments'] += $installmentAmount;
                }
            }
        }

        // Calculate total discount across all clients and months (for summary)
        $totalDiscount = 0;
        foreach ($monthTotals as $month => $totals) {
            $totalDiscount += $totals['all']['discount'];
        }

        return [
            'clients' => array_values($clientData),
            'months' => $months,
            'total_discount' => $totalDiscount,
            'month_totals' => $monthTotals,
        ];
    }

    private function contractOverlapsRange(Contract $contract, Carbon $start, Carbon $end): bool
    {
        $contractStart = Carbon::parse($contract->invoice_date)->startOfMonth();
        $contractEnd = $contractStart
            ->copy()
            ->addMonths(max($contract->duration_months - 1, 0))
            ->startOfMonth();

        return $contractStart <= $end && $contractEnd >= $start;
    }

    /**
     * Get pivot data as JSON (API endpoint)
     */
    public function apiPivot(Request $request)
    {
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->resolveDateFilters($request);
        $currency = $request->input('currency', '');
        $clientFilter = $request->input('client', '');
        $appFilter = $request->input('app_name', '');

        $pivotData = $this->generatePivotData($startDate, $endDate, $currency, $clientFilter, $appFilter);

        return response()->json($pivotData);
    }

    private function resolveDateFilters(Request $request): array
    {
        $period = trim((string) $request->input('period', ''));

        if ($period !== '') {
            try {
                $selectedMonth = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
                $normalizedPeriod = $selectedMonth->format('Y-m');

                return [
                    'period' => $normalizedPeriod,
                    'startDate' => $normalizedPeriod,
                    'endDate' => $normalizedPeriod,
                ];
            } catch (\Throwable $exception) {
                // Fall back to the explicit range filters below.
            }
        }

        return [
            'period' => '',
            'startDate' => (string) $request->input('start', Carbon::now()->startOfYear()->format('Y-m')),
            'endDate' => (string) $request->input('end', Carbon::now()->endOfYear()->format('Y-m')),
        ];
    }
}

