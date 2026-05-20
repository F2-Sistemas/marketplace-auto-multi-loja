<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PostController;

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
Route::get('/api/admin/stats', [AdminStatsController::class, 'index']);

// Central Admin Help Desk support tickets
Route::get('/api/admin/tickets', [SupportTicketController::class, 'adminIndex']);
Route::post('/api/admin/tickets/{id}/reply', [SupportTicketController::class, 'adminReply']);
Route::post('/api/admin/tickets/{id}/category', [SupportTicketController::class, 'updateCategory']);

// Storefront & Lojista Support Tickets
Route::get('/api/store/tickets', [SupportTicketController::class, 'storeIndex']);
Route::post('/api/store/tickets', [SupportTicketController::class, 'storeCreate']);
Route::get('/api/store/tickets/{id}', [SupportTicketController::class, 'show']);
Route::post('/api/store/tickets/{id}/reply', [SupportTicketController::class, 'storeReply']);
Route::post('/api/store/tickets/{id}/close', [SupportTicketController::class, 'close']);
Route::post('/api/store/tickets/{id}/rate', [SupportTicketController::class, 'rate']);
Route::post('/api/store/tickets/{id}/reopen', [SupportTicketController::class, 'reopen']);

// Storefront Subscription billing and theme custom styling
Route::get('/api/store/billing', [SubscriptionController::class, 'billingInfo']);
Route::post('/api/store/settings/theme', [SubscriptionController::class, 'updateTheme']);
Route::post('/api/store/vehicles/{id}/status', [VehicleController::class, 'updateStatus']);

// Customer Favoriting Features
Route::get('/api/favorites', [FavoriteController::class, 'index']);
Route::post('/api/favorites/{vehicle_id}/toggle', [FavoriteController::class, 'toggle']);

// Authentication, Password Recovery & Email Verification
Route::post('/api/auth/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/api/auth/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/api/auth/email/send-verification', [AuthController::class, 'sendVerification']);
Route::post('/api/auth/email/verify', [AuthController::class, 'verifyEmail']);
Route::put('/api/auth/profile', [AuthController::class, 'updateProfile']);

// News / Posts Endpoints
Route::get('/api/posts', [PostController::class, 'index']);
Route::get('/api/posts/{slug}', [PostController::class, 'show']);
Route::get('/api/admin/posts', [PostController::class, 'adminIndex']);
Route::post('/api/admin/posts', [PostController::class, 'store']);
Route::put('/api/admin/posts/{id}', [PostController::class, 'update']);
Route::delete('/api/admin/posts/{id}', [PostController::class, 'destroy']);
