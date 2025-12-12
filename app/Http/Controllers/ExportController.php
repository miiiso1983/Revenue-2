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
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportController extends Controller
{
    /**
     * Export pivot table to Excel
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start', Carbon::now()->startOfYear()->format('Y-m'));
        $endDate = $request->input('end', Carbon::now()->endOfYear()->format('Y-m'));
        $currency = $request->input('currency', '');
        $clientFilter = $request->input('client', '');
        $appFilter = $request->input('app_name', '');
        $dataType = $request->input('data_type', 'both');

        // Generate pivot data
        $pivotData = $this->generatePivotData($startDate, $endDate, $currency, $clientFilter, $appFilter);
        
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
                if (in_array($dataType, ['both', 'revenue'])) {
                    $cellParts[] = "Rev: " . number_format($monthData['revenue'], 2);
                }
                if (in_array($dataType, ['both', 'installments'])) {
                    $cellParts[] = "Inst: " . number_format($monthData['installments'], 2);
                }
                    if ($monthData['discount'] > 0) {
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
        $start = Carbon::parse($startDate . '-01');
        $end = Carbon::parse($endDate . '-01');
        
        $months = [];
        $current = $start->copy();
        while ($current <= $end) {
            $months[] = $current->format('Y-m-d');
            $current->addMonth();
        }
        
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
            
            if (!in_array($contract->invoice_number, $clientData[$clientName]['invoices'])) {
                $clientData[$clientName]['invoices'][] = $contract->invoice_number;
            }
            
            $allocations = MonthlyAllocation::where('contract_id', $contract->id)
                ->whereBetween('month_date', [$start->format('Y-m-01'), $end->format('Y-m-01')])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->month_date)->format('Y-m-d');
                });

            $installments = Installment::where('contract_id', $contract->id)
                ->whereBetween('due_date', [$start->format('Y-m-01'), $end->format('Y-m-01')])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->due_date)->format('Y-m-d');
                });
            
            foreach ($months as $month) {
                if (!isset($clientData[$clientName]['months'][$month])) {
                    $clientData[$clientName]['months'][$month] = [
                        'revenue' => 0,
                        'installments' => 0,
                        'discount' => 0,
                        'currency' => $contract->currency,
                    ];
                }

                if (isset($allocations[$month])) {
                    $clientData[$clientName]['months'][$month]['revenue'] += $allocations[$month]->allocated_amount;
                    $clientData[$clientName]['months'][$month]['discount'] += $allocations[$month]->discount_amount;
                }
                
                if (isset($installments[$month])) {
                    $clientData[$clientName]['months'][$month]['installments'] += $installments[$month]->installment_amount;
                }
            }
        }

        return [
            'clients' => array_values($clientData),
            'months' => $months,
        ];
    }
}

