<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\MonthlyAllocation;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display pivot table report
     */
    public function index(Request $request)
    {
        // Default date range: current year
        $startDate = $request->input('start', Carbon::now()->startOfYear()->format('Y-m'));
        $endDate = $request->input('end', Carbon::now()->endOfYear()->format('Y-m'));

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
        $start = Carbon::parse($startDate . '-01');
        $end = Carbon::parse($endDate . '-01');
        
        // Generate month columns
        $months = [];
        $current = $start->copy();
        while ($current <= $end) {
            $months[] = $current->format('Y-m-d');
            $current->addMonth();
        }
        
        // Get contracts with filters
        $contractsQuery = Contract::query();
        
        if ($currency) {
            $contractsQuery->where('currency', $currency);
        }
        
        if ($clientFilter) {
            $contractsQuery->where('client_name', 'like', '%' . $clientFilter . '%');
        }
        
        if ($appFilter) {
            $contractsQuery->where('app_name', 'like', '%' . $appFilter . '%');
        }
        
        $contracts = $contractsQuery->get();
        
        // Group by client
        $clientData = [];
        
        foreach ($contracts as $contract) {
            $clientName = $contract->client_name;
            
            if (!isset($clientData[$clientName])) {
                $clientData[$clientName] = [
                    'client_name' => $clientName,
                    'invoices' => [],
                    'months' => [],
                ];
            }
            
            // Add invoice number
            if (!in_array($contract->invoice_number, $clientData[$clientName]['invoices'])) {
                $clientData[$clientName]['invoices'][] = $contract->invoice_number;
            }
            
            // Get monthly allocations for this contract
            $allocations = MonthlyAllocation::where('contract_id', $contract->id)
                ->whereBetween('month_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->month_date)->startOfDay()->format('Y-m-d');
                });

            // Get installments for this contract
            $installments = Installment::where('contract_id', $contract->id)
                ->whereBetween('due_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->due_date)->startOfDay()->format('Y-m-d');
                });
            
            // Populate month data
            foreach ($months as $month) {
                if (!isset($clientData[$clientName]['months'][$month])) {
                    $clientData[$clientName]['months'][$month] = [
                        'revenue' => 0,
                        'installments' => 0,
                        'discount' => 0,
                        'currency' => $contract->currency,
                    ];
                }
                
                // Add revenue & discount
                if (isset($allocations[$month])) {
                    $clientData[$clientName]['months'][$month]['revenue'] += $allocations[$month]->allocated_amount;
                    $clientData[$clientName]['months'][$month]['discount'] += $allocations[$month]->discount_amount;
                }

                // Add installments
                if (isset($installments[$month])) {
                    $clientData[$clientName]['months'][$month]['installments'] += $installments[$month]->installment_amount;
                }
            }
        }

        // Calculate total discount across all clients and months (for summary)
        $totalDiscount = 0;
        foreach ($clientData as $client) {
            foreach ($months as $month) {
                if (isset($client['months'][$month])) {
                    $totalDiscount += $client['months'][$month]['discount'];
                }
            }
        }

        return [
            'clients' => array_values($clientData),
            'months' => $months,
            'total_discount' => $totalDiscount,
        ];
    }

    /**
     * Get pivot data as JSON (API endpoint)
     */
    public function apiPivot(Request $request)
    {
        $startDate = $request->input('start', Carbon::now()->startOfYear()->format('Y-m'));
        $endDate = $request->input('end', Carbon::now()->endOfYear()->format('Y-m'));
        $currency = $request->input('currency', '');
        $clientFilter = $request->input('client', '');
        $appFilter = $request->input('app_name', '');

        $pivotData = $this->generatePivotData($startDate, $endDate, $currency, $clientFilter, $appFilter);

        return response()->json($pivotData);
    }
}

