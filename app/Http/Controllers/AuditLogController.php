<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * Display audit logs (Admin only)
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('audit-logs.index', compact('logs'));
    }
}

