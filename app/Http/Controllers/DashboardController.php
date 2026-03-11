<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\MonthlyAllocation;
use App\Models\Installment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard with summary statistics
     */
    public function index(Request $request)
    {
        $selectedPeriod = $request->input('period', Carbon::now()->format('Y-m'));

        try {
            $selectedMonth = Carbon::createFromFormat('Y-m', $selectedPeriod)->startOfMonth();
        } catch (\Throwable $exception) {
            $selectedMonth = Carbon::now()->startOfMonth();
        }

        $selectedPeriod = $selectedMonth->format('Y-m');
        $selectedPeriodLabel = $selectedMonth->format('F Y');
        $selectedMonthDate = $selectedMonth->toDateString();
        
        // Total revenue for selected month
        $selectedMonthRevenueUSD = MonthlyAllocation::whereDate('month_date', $selectedMonthDate)
            ->where('currency', 'USD')
            ->sum('allocated_amount');
            
        $selectedMonthRevenueIQD = MonthlyAllocation::whereDate('month_date', $selectedMonthDate)
            ->where('currency', 'IQD')
            ->sum('allocated_amount');
        
        // Total installments due for selected month
        $selectedMonthInstallmentsUSD = Installment::whereYear('due_date', $selectedMonth->year)
            ->whereMonth('due_date', $selectedMonth->month)
            ->where('currency', 'USD')
            ->sum('installment_amount');
            
        $selectedMonthInstallmentsIQD = Installment::whereYear('due_date', $selectedMonth->year)
            ->whereMonth('due_date', $selectedMonth->month)
            ->where('currency', 'IQD')
            ->sum('installment_amount');
        
        // Active clients (unique client names)
        $activeClients = Contract::distinct('client_name')->count('client_name');
        
        // Active contracts
        $activeContracts = Contract::count();
        
        // Recent contracts
        $recentContracts = Contract::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'selectedPeriod',
            'selectedPeriodLabel',
            'selectedMonthRevenueUSD',
            'selectedMonthRevenueIQD',
            'selectedMonthInstallmentsUSD',
            'selectedMonthInstallmentsIQD',
            'activeClients',
            'activeContracts',
            'recentContracts'
        ));
    }
}

