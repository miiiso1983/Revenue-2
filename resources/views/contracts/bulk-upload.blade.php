@extends('layouts.app')

@section('title', 'Bulk Upload')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Bulk Upload Contracts</h1>
    <p class="text-gray-600 mt-1">Upload CSV or Excel file with multiple contracts</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upload Form -->
    <div class="card">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Upload File</h2>
        <form action="{{ route('bulk-upload.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="label">Select File (CSV or Excel)</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" 
                       class="input-field" required>
                @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Upload and Preview</button>
        </form>
    </div>

    <!-- Instructions -->
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">File Format Instructions</h2>
                <div class="space-y-3 text-sm">
                    <p class="font-semibold">Required Columns (in order):</p>
                    <ol class="list-decimal list-inside space-y-1 text-gray-700">
                        <li>app_name</li>
                        <li>client_name</li>
                        <li>invoice_number</li>
                        <li>invoice_date (YYYY-MM-DD)</li>
                        <li>duration_months (1-120)</li>
                        <li>amount (numeric)</li>
                        <li>currency (USD or IQD)</li>
                        <li>installment_frequency (monthly, quarterly, or yearly)</li>
                    </ol>

                    <p class="font-semibold mt-4">Optional Columns:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li><span class="font-mono">discount_type</span> ("percentage" or "fixed")</li>
                        <li><span class="font-mono">discount_value</span> (numeric, &ge; 0)</li>
                    </ul>

                    <div class="mt-4 p-3 bg-blue-50 rounded">
                        <p class="font-semibold text-blue-800">Example CSV:</p>
                        <pre class="text-xs mt-2 text-blue-900">app_name,client_name,invoice_number,invoice_date,duration_months,amount,currency,installment_frequency,discount_type,discount_value
ERP System,Acme Corp,INV-001,2025-01-01,12,12000.00,USD,monthly,percentage,10
CRM Software,Tech Ltd,INV-002,2025-02-01,6,3600.00,USD,quarterly,fixed,600</pre>
                    </div>

                    <div class="mt-4">
                        <a href="/sample-contracts.csv" class="text-blue-600 hover:underline" download>
                            📥 Download Sample CSV Template
                        </a>
                    </div>
                </div>
            </div>
</div>

<!-- Notes -->
<div class="card mt-6">
    <h3 class="font-bold text-gray-800 mb-2">Important Notes:</h3>
    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
        <li>The first row must contain column headers exactly as specified above</li>
        <li>Invoice numbers must be unique across all contracts</li>
        <li>Date format must be YYYY-MM-DD (e.g., 2025-01-15)</li>
        <li>Duration must be between 1 and 120 months</li>
        <li>Amount must be a positive number</li>
        <li>Currency must be either "USD" or "IQD"</li>
        <li>Installment frequency must be "monthly", "quarterly", or "yearly"</li>
        <li>Maximum file size: 10MB</li>
    </ul>
</div>
@endsection

