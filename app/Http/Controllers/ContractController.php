<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts
     */
    public function index(Request $request)
    {
        $query = Contract::with('creator');
        
        // Apply filters
        if ($request->filled('client')) {
            $query->where('client_name', 'like', '%' . $request->client . '%');
        }
        
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }
        
        if ($request->filled('app_name')) {
            $query->where('app_name', 'like', '%' . $request->app_name . '%');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%')
                  ->orWhere('app_name', 'like', '%' . $search . '%');
            });
        }
        
        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new contract
     */
    public function create()
    {
        return view('contracts.create');
    }

    /**
     * Store a newly created contract
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:255|unique:contracts,invoice_number',
            'invoice_date' => 'required|date',
            'duration_months' => 'required|integer|min:1|max:120',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,IQD',
            'installment_frequency' => 'required|in:monthly,quarterly,yearly',
        ]);
        
        $validated['created_by'] = auth()->id();
        
        DB::beginTransaction();
        try {
            $contract = Contract::create($validated);
            
            // Log the creation
            AuditLog::log('created', 'contracts', $contract->id, null, $contract->toArray());
            
            DB::commit();
            
            return redirect()->route('contracts.index')
                ->with('success', 'Contract created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating contract: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified contract
     */
    public function show(Contract $contract)
    {
        $contract->load(['monthlyAllocations', 'installments', 'creator']);
        
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified contract
     */
    public function edit(Contract $contract)
    {
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified contract
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:255|unique:contracts,invoice_number,' . $contract->id,
            'invoice_date' => 'required|date',
            'duration_months' => 'required|integer|min:1|max:120',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,IQD',
            'installment_frequency' => 'required|in:monthly,quarterly,yearly',
        ]);
        
        DB::beginTransaction();
        try {
            $oldValues = $contract->toArray();
            $contract->update($validated);
            
            // Log the update
            AuditLog::log('updated', 'contracts', $contract->id, $oldValues, $contract->fresh()->toArray());
            
            DB::commit();
            
            return redirect()->route('contracts.index')
                ->with('success', 'Contract updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating contract: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified contract
     */
    public function destroy(Contract $contract)
    {
        DB::beginTransaction();
        try {
            $oldValues = $contract->toArray();
            $contractId = $contract->id;
            
            $contract->delete();
            
            // Log the deletion
            AuditLog::log('deleted', 'contracts', $contractId, $oldValues, null);
            
            DB::commit();
            
            return redirect()->route('contracts.index')
                ->with('success', 'Contract deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting contract: ' . $e->getMessage());
        }
    }
}

