@extends('layouts.app')

@section('title', 'Pivot Report')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Revenue & Installment Pivot Report</h1>
    <p class="text-gray-600 mt-1">Monthly breakdown by client</p>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="{{ route('reports.pivot') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
        <div>
            <label class="label">Start Month</label>
            <input type="month" name="start" value="{{ $startDate }}" class="input-field">
        </div>
        <div>
            <label class="label">End Month</label>
            <input type="month" name="end" value="{{ $endDate }}" class="input-field">
        </div>
        <div>
            <label class="label">Client</label>
            <input type="text" name="client" value="{{ $clientFilter }}"
                   placeholder="Client name" class="input-field">
        </div>
        <div>
            <label class="label">App Name</label>
            <select name="app_name" class="input-field">
                <option value="">All Apps</option>
                @foreach($appNames as $appName)
                <option value="{{ $appName }}" {{ $appFilter == $appName ? 'selected' : '' }}>
                    {{ $appName }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Currency</label>
            <select name="currency" class="input-field">
                <option value="">All</option>
                <option value="USD" {{ $currency == 'USD' ? 'selected' : '' }}>USD</option>
                <option value="IQD" {{ $currency == 'IQD' ? 'selected' : '' }}>IQD</option>
            </select>
        </div>
        <div>
            <label class="label">Data Type</label>
            <select name="data_type" class="input-field">
                <option value="both" {{ $dataType == 'both' ? 'selected' : '' }}>Both</option>
                <option value="revenue" {{ $dataType == 'revenue' ? 'selected' : '' }}>Revenue Only</option>
                <option value="installments" {{ $dataType == 'installments' ? 'selected' : '' }}>Installments Only</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="{{ route('reports.pivot') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Export Buttons -->
<div class="mb-4 flex space-x-4">
    <a href="{{ route('export') }}?start={{ $startDate }}&end={{ $endDate }}&currency={{ $currency }}&client={{ $clientFilter }}&app_name={{ $appFilter }}&data_type={{ $dataType }}"
       class="btn-primary">
        📥 Export to Excel
    </a>
    <button onclick="window.print()" class="btn-secondary">
        🖨️ Print Report
    </button>
</div>

<!-- Pivot Table -->
<div class="card overflow-x-auto">
    @if(count($pivotData['clients']) > 0)
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 sticky top-0">
            <tr>
                <th class="border px-4 py-2 text-left font-semibold">Client Name</th>
                <th class="border px-4 py-2 text-left font-semibold">Invoice Numbers</th>
                @foreach($pivotData['months'] as $month)
                <th class="border px-4 py-2 text-center font-semibold whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($month)->format('M Y') }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($pivotData['clients'] as $client)
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2 font-semibold">{{ $client['client_name'] }}</td>
                <td class="border px-4 py-2 text-sm">
                    {{ implode(', ', $client['invoices']) }}
                </td>
                @foreach($pivotData['months'] as $month)
                <td class="border px-4 py-2 text-sm">
                    @php
                        $monthData = $client['months'][$month] ?? ['revenue' => 0, 'installments' => 0, 'currency' => ''];
                        $showRevenue = in_array($dataType, ['both', 'revenue']);
                        $showInstallments = in_array($dataType, ['both', 'installments']);
                        $hasData = ($showRevenue && $monthData['revenue'] > 0) || ($showInstallments && $monthData['installments'] > 0);
                    @endphp
                    @if($hasData)
                    <div class="text-center">
                        @if($showRevenue)
                        <div class="text-blue-600 font-semibold">
                            Rev: {{ number_format($monthData['revenue'], 2) }}
                        </div>
                        @endif
                        @if($showInstallments)
                        <div class="text-green-600">
                            Inst: {{ number_format($monthData['installments'], 2) }}
                        </div>
                        @endif
                        <div class="text-xs text-gray-500">{{ $monthData['currency'] }}</div>
                    </div>
                    @else
                    <div class="text-center text-gray-400">-</div>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="text-center py-12 text-gray-500">
        <p class="text-lg">No data found for the selected filters</p>
        <p class="text-sm mt-2">Try adjusting your filter criteria</p>
    </div>
    @endif
</div>

<!-- Legend -->
<div class="card mt-6">
    <h3 class="font-bold text-gray-800 mb-2">Legend:</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-blue-600 font-semibold">Rev:</span> 
            <span class="text-gray-700">Monthly revenue allocation for that month</span>
        </div>
        <div>
            <span class="text-green-600 font-semibold">Inst:</span> 
            <span class="text-gray-700">Installment amount due in that month</span>
        </div>
    </div>
</div>

<style>
    @media print {
        nav, footer, .no-print, button, a.btn-primary, a.btn-secondary {
            display: none !important;
        }
        .card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
@endsection

