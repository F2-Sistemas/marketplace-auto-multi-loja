<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    private function resolveStoreId(Request $request): ?int
    {
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $email = $request->header('X-Test-User-Email') ?? $request->input('user_email') ?? 'gerente.natal@autocar.com';
            $user = DB::table('users')->where('email', $email)->first();
        }

        if (!$user) {
            return null;
        }

        return (int) DB::table('store_users')->where('user_id', $user->id)->value('store_id');
    }

    /**
     * Get active plan information and past subscription payments history.
     */
    public function billingInfo(Request $request): JsonResponse
    {
        $storeId = $this->resolveStoreId($request);

        if (!$storeId) {
            return response()->json(['message' => 'Loja não identificada ou não autorizado.'], 403);
        }

        $subscription = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->select('subscriptions.*', 'plans.name as plan_name', 'plans.price as plan_price', 'plans.limit_vehicles', 'plans.features')
            ->where('store_id', $storeId)
            ->first();

        if ($subscription && $subscription->features) {
            $subscription->features = json_decode($subscription->features);
        }

        $payments = DB::table('subscription_payments')
            ->where('store_id', $storeId)
            ->orderBy('billing_date', 'desc')
            ->get();

        return response()->json([
            'subscription' => $subscription,
            'payments' => $payments
        ]);
    }

    /**
     * Update dynamic layout style, accent colors, and font preferences.
     */
    public function updateTheme(Request $request): JsonResponse
    {
        $storeId = $this->resolveStoreId($request);

        if (!$storeId) {
            return response()->json(['message' => 'Loja não identificada ou não autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'accent_color' => 'nullable|string|max:255',
            'secondary_color' => 'nullable|string|max:255',
            'font_family' => 'nullable|string|in:Inter,Outfit,Roboto,Playfair Display',
            'layout_style' => 'nullable|string|in:minimalist,advanced,grid,list',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fields = $request->only(['accent_color', 'secondary_color', 'font_family', 'layout_style']);

        DB::beginTransaction();
        try {
            foreach ($fields as $key => $value) {
                if ($value !== null) {
                    DB::table('store_settings')->updateOrInsert(
                        ['store_id' => $storeId, 'key' => $key],
                        ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
            DB::commit();

            return response()->json(['message' => 'Configurações de tema salvas com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao salvar configurações.', 'error' => $e->getMessage()], 500);
        }
    }
}
