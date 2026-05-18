<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    /**
     * Get the active tenant information.
     */
    public function show(TenantManager $tenantManager): JsonResponse
    {
        $store = $tenantManager->getStore();

        if ($store === null) {
            return response()->json([
                'is_portal' => true,
                'name' => 'AutoHub Central',
                'description' => 'O portal central de veículos premium do Brasil.',
            ]);
        }

        // Fetch settings from store_settings
        $settingsRaw = DB::table('store_settings')
            ->where('store_id', $store->id)
            ->get();

        $settings = [];
        foreach ($settingsRaw as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        // Fetch domains
        $domains = DB::table('store_domains')
            ->where('store_id', $store->id)
            ->get(['domain', 'type', 'is_primary', 'status']);

        return response()->json([
            'is_portal' => false,
            'id' => $store->id,
            'public_id' => $store->public_id,
            'name' => $store->name,
            'slug' => $store->slug,
            'status' => $store->status,
            'settings' => [
                'logo_url' => $settings['logo_url'] ?? 'https://api.rederevenda.com/images/default-logo.png',
                'whatsapp_number' => $settings['whatsapp_number'] ?? '5584999999999',
                'accent_color' => $settings['accent_color'] ?? '#4f46e5',
                'address' => $settings['address'] ?? 'Av. Salgado Filho, 1200 - Natal/RN',
                'phone' => $settings['phone'] ?? '(84) 3222-2222',
            ],
            'domains' => $domains,
        ]);
    }
}
