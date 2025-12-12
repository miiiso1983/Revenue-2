@extends('layouts.app')

@section('title', 'Contracts')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Contracts</h1>
        <p class="text-gray-600 mt-1">Manage all contracts and invoices</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('contracts.create') }}" class="btn-primary">
        + Add New Contract
    </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="{{ route('contracts.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Invoice #, Client, App..." class="input-field">
        </div>
        <div>
            <label class="label">Client</label>
            <input type="text" name="client" value="{{ request('client') }}" 
                   placeholder="Client name" class="input-field">
        </div>
        <div>
            <label class="label">Currency</label>
            <select name="currency" class="input-field">
                <option value="">All</option>
                <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                <option value="IQD" {{ request('currency') == 'IQD' ? 'selected' : '' }}>IQD</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="{{ route('contracts.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Contracts Table -->
<div class="card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client Name</th>
                    <th>App Name</th>
                    <th>Amount</th>
                    <th>Duration</th>
                    <th>Frequency</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td class="font-semibold">{{ $contract->invoice_number }}</td>
                    <td>{{ $contract->client_name }}</td>
                    <td>{{ $contract->app_name }}</td>
                    <td>{{ number_format($contract->amount, 2) }} {{ $contract->currency }}</td>
                    <td>{{ $contract->duration_months }} months</td>
                    <td>{{ ucfirst($contract->installment_frequency) }}</td>
                    <td>{{ $contract->invoice_date->format('M d, Y') }}</td>
                    <td>
                        <div class="flex space-x-2">
                            <a href="{{ route('contracts.show', $contract) }}" 
                               class="text-blue-600 hover:text-blue-800">View</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('contracts.edit', $contract) }}" 
                               class="text-green-600 hover:text-green-800">Edit</a>
                            <form action="{{ route('contracts.destroy', $contract) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirmDelete('Are you sure you want to delete this contract?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-gray-500 py-8">No contracts found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>
@endsection

