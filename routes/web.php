<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AuditLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin-only routes (must come before wildcard routes)
    Route::middleware('admin')->group(function () {
        // Contract management
        Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

        // Bulk upload
        Route::get('/bulk-upload', [BulkUploadController::class, 'index'])->name('bulk-upload.index');
        Route::post('/bulk-upload', [BulkUploadController::class, 'upload'])->name('bulk-upload.upload');
        Route::post('/bulk-upload/import', [BulkUploadController::class, 'import'])->name('bulk-upload.import');

        // Audit logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Contracts (Read-only for guests, full CRUD for admins)
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');

    // Reports
    Route::get('/reports/pivot', [ReportController::class, 'index'])->name('reports.pivot');

    // Export
    Route::get('/export', [ExportController::class, 'export'])->name('export');
});

