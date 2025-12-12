@extends('layouts.app')

@section('title', 'Edit Contract')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Contract</h1>
    <p class="text-gray-600 mt-1">Update contract information</p>
</div>

<div class="card max-w-3xl">
    <form action="{{ route('contracts.update', $contract) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- App Name -->
            <div>
                <label for="app_name" class="label">Application Name <span class="text-red-500">*</span></label>
                <select id="app_name" name="app_name" class="input-field" required>
                    <option value="">Select Application</option>
                    <option value="Teami Pro" {{ old('app_name', $contract->app_name) == 'Teami Pro' ? 'selected' : '' }}>Teami Pro</option>
                    <option value="MaxCon" {{ old('app_name', $contract->app_name) == 'MaxCon' ? 'selected' : '' }}>MaxCon</option>
                    <option value="ConCure" {{ old('app_name', $contract->app_name) == 'ConCure' ? 'selected' : '' }}>ConCure</option>
                    <option value="MediCon" {{ old('app_name', $contract->app_name) == 'MediCon' ? 'selected' : '' }}>MediCon</option>
                    <option value="Connect Job" {{ old('app_name', $contract->app_name) == 'Connect Job' ? 'selected' : '' }}>Connect Job</option>
                </select>
                @error('app_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Client Name -->
            <div>
                <label for="client_name" class="label">Client Name <span class="text-red-500">*</span></label>
                <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $contract->client_name) }}" 
                       class="input-field" required>
                @error('client_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Invoice Number -->
            <div>
                <label for="invoice_number" class="label">Invoice Number <span class="text-red-500">*</span></label>
                <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $contract->invoice_number) }}" 
                       class="input-field" required>
                @error('invoice_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Invoice Date -->
            <div>
                <label for="invoice_date" class="label">Invoice Date <span class="text-red-500">*</span></label>
                <input type="date" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $contract->invoice_date->format('Y-m-d')) }}" 
                       class="input-field" required>
                @error('invoice_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_months" class="label">Duration (Months) <span class="text-red-500">*</span></label>
                <input type="number" id="duration_months" name="duration_months" value="{{ old('duration_months', $contract->duration_months) }}" 
                       min="1" max="120" class="input-field" required>
                @error('duration_months')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="label">Total Amount <span class="text-red-500">*</span></label>
                <input type="number" id="amount" name="amount" value="{{ old('amount', $contract->amount) }}" 
                       step="0.01" min="0" class="input-field" required>
                @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Currency -->
            <div>
                <label for="currency" class="label">Currency <span class="text-red-500">*</span></label>
                <select id="currency" name="currency" class="input-field" required>
                    <option value="USD" {{ old('currency', $contract->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="IQD" {{ old('currency', $contract->currency) == 'IQD' ? 'selected' : '' }}>IQD</option>
                </select>
                @error('currency')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Installment Frequency -->
            <div>
                <label for="installment_frequency" class="label">Installment Frequency <span class="text-red-500">*</span></label>
                <select id="installment_frequency" name="installment_frequency" class="input-field" required>
                    <option value="monthly" {{ old('installment_frequency', $contract->installment_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ old('installment_frequency', $contract->installment_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="yearly" {{ old('installment_frequency', $contract->installment_frequency) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('installment_frequency')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

	            <!-- Discount Type (optional) -->
	            <div>
	                <label for="discount_type" class="label">Discount Type</label>
	                <select id="discount_type" name="discount_type" class="input-field">
	                    <option value="">No Discount</option>
	                    <option value="percentage" {{ old('discount_type', $contract->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
	                    <option value="fixed" {{ old('discount_type', $contract->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
	                </select>
	                @error('discount_type')
	                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
	                @enderror
	            </div>

	            <!-- Discount Value (optional) -->
	            <div>
	                <label for="discount_value" class="label">Discount Value</label>
	                <input type="number" id="discount_value" name="discount_value" value="{{ old('discount_value', $contract->discount_value) }}"
	                       step="0.01" min="0" class="input-field">
	                @error('discount_value')
	                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
	                @enderror
	            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex space-x-4">
            <button type="submit" class="btn-primary">Update Contract</button>
            <a href="{{ route('contracts.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

