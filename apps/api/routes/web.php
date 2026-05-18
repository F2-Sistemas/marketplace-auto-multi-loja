<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\AuthController;

Route::get('/', fn () => response()->json([
    'app' => 'AutoHub API Core',
    'status' => 'online',
    'version' => '1.0.0'
]));

// Tenant Configuration Endpoint
Route::get('/api/tenant', [TenantController::class, 'show']);

// Public Vehicles Endpoints
Route::get('/api/vehicles', [VehicleController::class, 'index']);
Route::get('/api/vehicles/{id}', [VehicleController::class, 'show']);

// Client Lead Submission
Route::post('/api/leads', [LeadController::class, 'store']);

// Central Admin Dashboard / Backoffice Endpoints
Route::get('/api/admin/stores', [AdminStoreController::class, 'index']);
Route::post('/api/admin/stores', [AdminStoreController::class, 'store']);
Route::post('/api/admin/stores/{id}/toggle', [AdminStoreController::class, 'toggle']);

// Authentication, Password Recovery & Email Verification
Route::post('/api/auth/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/api/auth/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/api/auth/email/send-verification', [AuthController::class, 'sendVerification']);
Route::post('/api/auth/email/verify', [AuthController::class, 'verifyEmail']);
