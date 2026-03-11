<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\MonthlyAllocation;
use App\Models\Installment;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportController extends Controller
{
    /**
     * Export pivot table to Excel
     */
    public function export(Request $request)
    {
        ['startDate' => $startDate, 'endDate' => $endDate] = $this->resolveDateFilters($request);
        $currency = $request->input('currency', '');
        $clientFilter = $request->input('client', '');
        $appFilter = $request->input('app_name', '');
        $dataType = $request->input('data_type', 'both');

        // Generate pivot data
        $pivotData = $this->generatePivotData($startDate, $endDate, $currency, $clientFilter, $appFilter);
        $showRevenue = in_array($dataType, ['both', 'revenue'], true);
        $showInstallments = in_array($dataType, ['both', 'installments'], true);
        $showDiscount = in_array($dataType, ['both', 'discount'], true);
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setTitle('Revenue Report');
        
        // Add header information
        $sheet->setCellValue('A1', 'Revenue and Installment Report');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Period: ' . $startDate . ' to ' . $endDate);
        $sheet->mergeCells('A2:E2');
        
        $infoRow = 3;
        if ($currency) {
            $sheet->setCellValue('A' . $infoRow, 'Currency: ' . $currency);
            $sheet->mergeCells('A' . $infoRow . ':E' . $infoRow);
            $infoRow++;
        }
        if ($appFilter) {
            $sheet->setCellValue('A' . $infoRow, 'App: ' . $appFilter);
            $sheet->mergeCells('A' . $infoRow . ':E' . $infoRow);
            $infoRow++;
        }
        if ($dataType != 'both') {
            if ($dataType === 'revenue') {
                $dataTypeLabel = 'Revenue Only';
            } elseif ($dataType === 'installments') {
                $dataTypeLabel = 'Installments Only';
            } elseif ($dataType === 'discount') {
                $dataTypeLabel = 'Discount Only';
            } else {
                $dataTypeLabel = 'Filtered';
            }

            $sheet->setCellValue('A' . $infoRow, 'Data Type: ' . $dataTypeLabel);
            $sheet->mergeCells('A' . $infoRow . ':E' . $infoRow);
            $infoRow++;
        }

        // Start data from next row
        $currentRow = $infoRow + 1;
        
        // Headers
        $sheet->setCellValue('A' . $currentRow, 'Client Name');
        $sheet->setCellValue('B' . $currentRow, 'Invoice Numbers');

        $colIndex = 3; // Column C
        foreach ($pivotData['months'] as $month) {
            $monthLabel = Carbon::parse($month)->format('M Y');
            $sheet->setCellValueByColumnAndRow($colIndex, $currentRow, $monthLabel);
            $colIndex++;
        }

        // Style headers
        $lastColIndex = 2 + count($pivotData['months']); // B + number of months
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
        $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)
            ->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        $currentRow++;
        
        // Data rows
        foreach ($pivotData['clients'] as $client) {
            $sheet->setCellValue('A' . $currentRow, $client['client_name']);
            $sheet->setCellValue('B' . $currentRow, implode(', ', $client['invoices']));

            $colIndex = 3; // Column C
            foreach ($pivotData['months'] as $month) {
                $monthData = $client['months'][$month] ?? ['revenue' => 0, 'installments' => 0, 'discount' => 0];

                // Build cell value based on data type filter
                $cellParts = [];
                if ($showRevenue) {
                    $cellParts[] = "Rev: " . number_format($monthData['revenue'], 2);
                }
                if ($showInstallments) {
                    $cellParts[] = "Inst: " . number_format($monthData['installments'], 2);
                }
                if ($showDiscount && $monthData['discount'] > 0) {
                    $cellParts[] = "Disc: " . number_format($monthData['discount'], 2);
                }
                $cellValue = implode("\n", $cellParts);

                $sheet->setCellValueByColumnAndRow($colIndex, $currentRow, $cellValue);
                $sheet->getStyleByColumnAndRow($colIndex, $currentRow)->getAlignment()->setWrapText(true);
                $colIndex++;
            }

            $currentRow++;
        }

        // Auto-size columns
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
        
        // Generate filename
        $filename = 'revenue_report_' . date('Y-m-d_His') . '.xlsx';
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        
        // Return download response
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Generate pivot table data (same as ReportController)
     */
    private function generatePivotData($startDate, $endDate, $currency = '', $clientFilter = '', $appFilter = '')
    {
        $start = Carbon::parse("{$startDate}-01")->startOfMonth();
        $endMonth = Carbon::parse("{$endDate}-01")->startOfMonth();
        $rangeEnd = $endMonth->copy()->endOfMonth();
        
        $months = [];
        $current = $start->copy();
        while ($current <= $endMonth) {
            $months[] = $current->format('Y-m-d');
            $current->addMonth();
        }
        
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

        $clientData = [];
        
        foreach ($contracts as $contract) {
            $rowKey = (string) $contract->getKey();
            $clientName = (string) $contract->client_name;

            $clientData[$rowKey] = [
                'client_name' => $clientName,
                'invoices' => [(string) $contract->invoice_number],
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

            foreach ($months as $month) {
                $clientData[$rowKey]['months'][$month] = [
                    'revenue' => 0,
                    'installments' => 0,
                    'discount' => 0,
                    'currency' => (string) $contract->currency,
                ];

                $allocationValues = $allocations->get($month, ['revenue' => 0, 'discount' => 0]);
                $clientData[$rowKey]['months'][$month]['revenue'] = (float) $allocationValues['revenue'];
                $clientData[$rowKey]['months'][$month]['discount'] = (float) $allocationValues['discount'];
                $clientData[$rowKey]['months'][$month]['installments'] = (float) $installments->get($month, 0);
            }
        }

        return [
            'clients' => array_values($clientData),
            'months' => $months,
        ];
    }

    private function contractOverlapsRange(Contract $contract, Carbon $start, Carbon $end): bool
    {
        $contractStart = Carbon::parse($contract->invoice_date)->startOfMonth();
        $contractEnd = $contractStart
            ->copy()
            ->addMonths(max(((int) $contract->duration_months) - 1, 0))
            ->startOfMonth();

        return $contractStart <= $end && $contractEnd >= $start;
    }

    private function resolveDateFilters(Request $request): array
    {
        $period = trim((string) $request->input('period', ''));

        if ($period !== '') {
            try {
                $selectedMonth = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
                $normalizedPeriod = $selectedMonth->format('Y-m');

                return [
                    'startDate' => $normalizedPeriod,
                    'endDate' => $normalizedPeriod,
                ];
            } catch (\Throwable $exception) {
                // Fall back to the explicit range filters below.
            }
        }

        return [
            'startDate' => (string) $request->input('start', Carbon::now()->startOfYear()->format('Y-m')),
            'endDate' => (string) $request->input('end', Carbon::now()->endOfYear()->format('Y-m')),
        ];
    }
}

