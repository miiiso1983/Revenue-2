@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600 mt-1">Overview of revenue and installments</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Current Month Revenue USD -->
    <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Current Month Revenue (USD)</p>
                <p class="text-3xl font-bold mt-2">${{ number_format($currentMonthRevenueUSD, 2) }}</p>
            </div>
            <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Current Month Revenue IQD -->
    <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Current Month Revenue (IQD)</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($currentMonthRevenueIQD, 0) }}</p>
            </div>
            <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Current Month Installments USD -->
    <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Due Installments (USD)</p>
                <p class="text-3xl font-bold mt-2">${{ number_format($currentMonthInstallmentsUSD, 2) }}</p>
            </div>
            <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
    </div>

    <!-- Current Month Installments IQD -->
    <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Due Installments (IQD)</p>
                <p class="text-3xl font-bold mt-2">{{ number_format($currentMonthInstallmentsIQD, 0) }}</p>
            </div>
            <svg class="w-12 h-12 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Active Clients</h3>
        <p class="text-4xl font-bold text-blue-600">{{ $activeClients }}</p>
    </div>

    <div class="card">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Active Contracts</h3>
        <p class="text-4xl font-bold text-purple-600">{{ $activeContracts }}</p>
    </div>

    <div class="card">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Revenue</h3>
        <p class="text-2xl font-bold text-green-600">${{ number_format($totalRevenueUSD, 2) }}</p>
        <p class="text-xl font-bold text-green-600 mt-1">{{ number_format($totalRevenueIQD, 0) }} IQD</p>
    </div>
</div>

<!-- Recent Contracts -->
<div class="card">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Contracts</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>App Name</th>
                    <th>Amount</th>
                    <th>Duration</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentContracts as $contract)
                <tr>
                    <td class="font-semibold">{{ $contract->invoice_number }}</td>
                    <td>{{ $contract->client_name }}</td>
                    <td>{{ $contract->app_name }}</td>
                    <td>{{ number_format($contract->amount, 2) }} {{ $contract->currency }}</td>
                    <td>{{ $contract->duration_months }} months</td>
                    <td>{{ $contract->invoice_date->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-8">No contracts found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

