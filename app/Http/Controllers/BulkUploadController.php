<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkUploadController extends Controller
{
    /**
     * Show bulk upload form
     */
    public function index()
    {
        return view('contracts.bulk-upload');
    }

    /**
     * Process bulk upload file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);
        
        try {
            $file = $request->file('file');
            $data = $this->parseFile($file);
            
            // Validate data
            $validatedData = $this->validateData($data);
            
            // Store in session for preview
            session(['bulk_upload_data' => $validatedData]);
            
            return view('contracts.bulk-preview', ['data' => $validatedData]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Confirm and import bulk data
     */
    public function import(Request $request)
    {
        $data = session('bulk_upload_data');
        
        if (!$data) {
            return redirect()->route('bulk-upload.index')
                ->with('error', 'No data to import. Please upload a file first.');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if ($row['valid']) {
                    try {
                        $contract = Contract::create([
                            'app_name' => $row['app_name'],
                            'client_name' => $row['client_name'],
                            'invoice_number' => $row['invoice_number'],
                            'invoice_date' => $row['invoice_date'],
                            'duration_months' => $row['duration_months'],
                            'amount' => $row['amount'],
                            'currency' => $row['currency'],
                            'installment_frequency' => $row['installment_frequency'],
                            'created_by' => auth()->id(),
                        ]);
                        
                        AuditLog::log('created', 'contracts', $contract->id, null, $contract->toArray());
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = "Row {$row['row_number']}: " . $e->getMessage();
                    }
                }
            }
            
            DB::commit();
            session()->forget('bulk_upload_data');
            
            $message = "Import completed! {$successCount} contracts created.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} errors occurred.";
            }
            
            return redirect()->route('contracts.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Parse uploaded file (CSV or Excel)
     */
    private function parseFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $data = [];
        
        if ($extension === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($header)) {
                    $data[] = array_combine($header, $row);
                }
            }
            
            fclose($handle);
        } else {
            // Excel file
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $header = array_shift($rows);
            
            foreach ($rows as $row) {
                if (count($row) === count($header) && !empty(array_filter($row))) {
                    $data[] = array_combine($header, $row);
                }
            }
        }
        
        return $data;
    }

    /**
     * Validate bulk upload data
     */
    private function validateData($data)
    {
        $validated = [];
        $rowNumber = 1;
        
        $requiredFields = ['app_name', 'client_name', 'invoice_number', 'invoice_date', 
                          'duration_months', 'amount', 'currency', 'installment_frequency'];
        
        foreach ($data as $row) {
            $rowNumber++;
            $errors = [];
            $isValid = true;
            
            // Check required fields
            foreach ($requiredFields as $field) {
                if (!isset($row[$field]) || empty($row[$field])) {
                    $errors[] = "Missing {$field}";
                    $isValid = false;
                }
            }
            
            // Validate data types and values
            if (isset($row['duration_months']) && (!is_numeric($row['duration_months']) || $row['duration_months'] < 1 || $row['duration_months'] > 120)) {
                $errors[] = "Invalid duration_months (must be 1-120)";
                $isValid = false;
            }
            
            if (isset($row['amount']) && (!is_numeric($row['amount']) || $row['amount'] < 0)) {
                $errors[] = "Invalid amount";
                $isValid = false;
            }
            
            if (isset($row['currency']) && !in_array($row['currency'], ['USD', 'IQD'])) {
                $errors[] = "Invalid currency (must be USD or IQD)";
                $isValid = false;
            }
            
            if (isset($row['installment_frequency']) && !in_array($row['installment_frequency'], ['monthly', 'quarterly', 'yearly'])) {
                $errors[] = "Invalid installment_frequency";
                $isValid = false;
            }
            
            // Check for duplicate invoice number
            if (isset($row['invoice_number']) && Contract::where('invoice_number', $row['invoice_number'])->exists()) {
                $errors[] = "Duplicate invoice_number";
                $isValid = false;
            }
            
            $validated[] = array_merge($row, [
                'row_number' => $rowNumber,
                'valid' => $isValid,
                'errors' => $errors,
            ]);
        }
        
        return $validated;
    }
}

