@extends('layouts.app')

@section('title', 'Preview Bulk Upload')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Preview Bulk Upload</h1>
    <p class="text-gray-600 mt-1">Review the data before importing</p>
</div>

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card bg-blue-50">
        <p class="text-sm text-gray-600">Total Rows</p>
        <p class="text-2xl font-bold text-blue-600">{{ count($data) }}</p>
    </div>
    <div class="card bg-green-50">
        <p class="text-sm text-gray-600">Valid Rows</p>
        <p class="text-2xl font-bold text-green-600">{{ collect($data)->where('valid', true)->count() }}</p>
    </div>
    <div class="card bg-red-50">
        <p class="text-sm text-gray-600">Invalid Rows</p>
        <p class="text-2xl font-bold text-red-600">{{ collect($data)->where('valid', false)->count() }}</p>
    </div>
</div>

<!-- Preview Table -->
<div class="card mb-6">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Status</th>
                    <th>App Name</th>
                    <th>Client</th>
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th>Duration</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Frequency</th>
                    <th>Errors</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr class="{{ $row['valid'] ? 'bg-green-50' : 'bg-red-50' }}">
                    <td>{{ $row['row_number'] }}</td>
                    <td>
                        @if($row['valid'])
                        <span class="text-green-600 font-bold">✓ Valid</span>
                        @else
                        <span class="text-red-600 font-bold">✗ Invalid</span>
                        @endif
                    </td>
                    <td>{{ $row['app_name'] ?? '-' }}</td>
                    <td>{{ $row['client_name'] ?? '-' }}</td>
                    <td>{{ $row['invoice_number'] ?? '-' }}</td>
                    <td>{{ $row['invoice_date'] ?? '-' }}</td>
                    <td>{{ $row['duration_months'] ?? '-' }}</td>
                    <td>{{ $row['amount'] ?? '-' }}</td>
                    <td>{{ $row['currency'] ?? '-' }}</td>
                    <td>{{ $row['installment_frequency'] ?? '-' }}</td>
                    <td>
                        @if(!empty($row['errors']))
                        <ul class="text-xs text-red-600">
                            @foreach($row['errors'] as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Actions -->
<div class="flex space-x-4">
    @if(collect($data)->where('valid', true)->count() > 0)
    <form action="{{ route('bulk-upload.import') }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary">
            Import {{ collect($data)->where('valid', true)->count() }} Valid Contracts
        </button>
    </form>
    @endif
    <a href="{{ route('bulk-upload.index') }}" class="btn-secondary">Cancel</a>
</div>

@if(collect($data)->where('valid', false)->count() > 0)
<div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
    <p class="text-yellow-800 font-semibold">⚠️ Warning</p>
    <p class="text-sm text-yellow-700 mt-1">
        {{ collect($data)->where('valid', false)->count() }} row(s) contain errors and will be skipped during import.
        Please fix the errors and upload again if you want to import all data.
    </p>
</div>
@endif
@endsection

