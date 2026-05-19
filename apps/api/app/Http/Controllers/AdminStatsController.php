<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    /**
     * Get central dashboard statistics with caching.
     */
    public function index(Request $request): JsonResponse
    {
        $refresh = $request->query('refresh') === '1' || $request->query('refresh') === 'true';

        if ($refresh) {
            Cache::forget('admin_dashboard_stats');
        }

        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            $totalStores = DB::table('stores')->count();
            $activeStores = DB::table('stores')->where('status', 'active')->count();
            $totalVehicles = DB::table('vehicles')->where('status', 'active')->count();
            $totalLeads = DB::table('leads')->count();
            
            // Monthly revenue from SaaS subscriptions (payments in the current month)
            $monthlyRevenue = DB::table('subscription_payments')
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount');

            // Growth percentage calculations (mock comparison for UI depth)
            return [
                'total_stores' => $totalStores,
                'active_stores' => $activeStores,
                'total_vehicles' => $totalVehicles,
                'total_leads' => $totalLeads,
                'monthly_revenue' => (float) $monthlyRevenue,
                'cached_at' => now()->toIso8601String(),
                'growth' => [
                    'stores' => 12.5,
                    'vehicles' => 18.2,
                    'leads' => 24.1,
                    'revenue' => 15.8,
                ]
            ];
        });

        return response()->json($stats);
    }
}
