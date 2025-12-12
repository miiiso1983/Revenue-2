@extends('layouts.app')

@section('title', 'Contract Details')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Contract Details</h1>
        <p class="text-gray-600 mt-1">{{ $contract->invoice_number }}</p>
    </div>
    <a href="{{ route('contracts.index') }}" class="btn-secondary">Back to List</a>
</div>

<!-- Contract Information -->
<div class="card mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Contract Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-600">Application Name</p>
            <p class="text-lg font-semibold">{{ $contract->app_name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Client Name</p>
            <p class="text-lg font-semibold">{{ $contract->client_name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Invoice Number</p>
            <p class="text-lg font-semibold">{{ $contract->invoice_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Invoice Date</p>
            <p class="text-lg font-semibold">{{ $contract->invoice_date->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Duration</p>
            <p class="text-lg font-semibold">{{ $contract->duration_months }} months</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Total Amount</p>
            <p class="text-lg font-semibold">{{ number_format($contract->amount, 2) }} {{ $contract->currency }}</p>
        </div>
	        <div>
	            <p class="text-sm text-gray-600">Discount</p>
	            <p class="text-lg font-semibold">
	                @if($contract->discount_type && $contract->discount_value !== null)
	                    @if($contract->discount_type === 'percentage')
	                        {{ number_format($contract->discount_value, 2) }}%
	                    @else
	                        {{ number_format($contract->discount_value, 2) }} {{ $contract->currency }}
	                    @endif
	                @else
	                    None
	                @endif
	            </p>
	        </div>
        <div>
            <p class="text-sm text-gray-600">Installment Frequency</p>
            <p class="text-lg font-semibold">{{ ucfirst($contract->installment_frequency) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Created By</p>
            <p class="text-lg font-semibold">{{ $contract->creator->username ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- Monthly Allocations -->
<div class="card mb-6">
	    <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Revenue Allocations</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Allocated Amount</th>
	                    <th>Discount Amount</th>
                    <th>Currency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->monthlyAllocations as $allocation)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($allocation->month_date)->format('F Y') }}</td>
                    <td>{{ number_format($allocation->allocated_amount, 2) }}</td>
	                    <td>{{ number_format($allocation->discount_amount, 2) }}</td>
                    <td>{{ $allocation->currency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Installments -->
<div class="card">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Installment Schedule</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Due Date</th>
                    <th>Installment Amount</th>
                    <th>Currency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->installments as $installment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($installment->due_date)->format('F d, Y') }}</td>
                    <td>{{ number_format($installment->installment_amount, 2) }}</td>
                    <td>{{ $installment->currency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

