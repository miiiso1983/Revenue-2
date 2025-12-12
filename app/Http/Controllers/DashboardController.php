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
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m-01');
        
        // Total revenue for current month
        $currentMonthRevenueUSD = MonthlyAllocation::where('month_date', $currentMonth)
            ->where('currency', 'USD')
            ->sum('allocated_amount');
            
        $currentMonthRevenueIQD = MonthlyAllocation::where('month_date', $currentMonth)
            ->where('currency', 'IQD')
            ->sum('allocated_amount');
        
        // Total installments due this month
        $currentMonthInstallmentsUSD = Installment::whereYear('due_date', Carbon::now()->year)
            ->whereMonth('due_date', Carbon::now()->month)
            ->where('currency', 'USD')
            ->sum('installment_amount');
            
        $currentMonthInstallmentsIQD = Installment::whereYear('due_date', Carbon::now()->year)
            ->whereMonth('due_date', Carbon::now()->month)
            ->where('currency', 'IQD')
            ->sum('installment_amount');
        
        // Active clients (unique client names)
        $activeClients = Contract::distinct('client_name')->count('client_name');
        
        // Active contracts
        $activeContracts = Contract::count();
        
        // Total revenue by currency
        $totalRevenueUSD = Contract::where('currency', 'USD')->sum('amount');
        $totalRevenueIQD = Contract::where('currency', 'IQD')->sum('amount');
        
        // Recent contracts
        $recentContracts = Contract::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'currentMonthRevenueUSD',
            'currentMonthRevenueIQD',
            'currentMonthInstallmentsUSD',
            'currentMonthInstallmentsIQD',
            'activeClients',
            'activeContracts',
            'totalRevenueUSD',
            'totalRevenueIQD',
            'recentContracts'
        ));
    }
}

