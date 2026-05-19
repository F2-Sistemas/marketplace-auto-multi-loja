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
Route::post('/api/vehicles/search', [VehicleController::class, 'search']);
Route::get('/api/vehicles/{id}', [VehicleController::class, 'show']);

// Auxiliary Helpers for Filters
Route::get('/api/brands', function () {
    return response()->json(\App\Models\Brand::orderBy('name', 'asc')->get());
});

Route::get('/api/states', function () {
    return response()->json(\App\Models\State::orderBy('name', 'asc')->get());
});

Route::get('/api/cities', function () {
    return response()->json(\App\Models\City::with('state')->orderBy('name', 'asc')->get());
});

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
