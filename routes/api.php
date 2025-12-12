<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Contracts
    Route::get('/contracts', [ContractController::class, 'index']);
    Route::get('/contracts/{contract}', [ContractController::class, 'show']);
    
    // Reports
    Route::get('/reports/pivot', [ReportController::class, 'apiPivot']);
    
    // Admin-only API routes
    Route::middleware('admin')->group(function () {
        Route::post('/contracts', [ContractController::class, 'store']);
        Route::put('/contracts/{contract}', [ContractController::class, 'update']);
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy']);
    });
});

