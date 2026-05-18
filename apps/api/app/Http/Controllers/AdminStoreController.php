<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminStoreController extends Controller
{
    /**
     * List all stores with domains and settings.
     */
    public function index(): JsonResponse
    {
        $stores = DB::table('stores')->get();
        $response = [];

        foreach ($stores as $store) {
            // Get domains
            $domains = DB::table('store_domains')
                ->where('store_id', $store->id)
                ->get(['domain', 'type', 'is_primary', 'status']);

            // Get settings
            $settingsRaw = DB::table('store_settings')
                ->where('store_id', $store->id)
                ->get();

            $settings = [];

            foreach ($settingsRaw as $s) {
                $settings[$s->key] = $s->value;
            }

            $response[] = [
                'id' => $store->id,
                'public_id' => $store->public_id,
                'name' => $store->name,
                'slug' => $store->slug,
                'status' => $store->status,
                'created_at' => $store->created_at,
                'settings' => $settings,
                'domains' => $domains,
            ];
        }

        return response()->json($response);
    }

    /**
     * Create a new store (Tenant Provisioning).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:store_domains,domain',
            'whatsapp_number' => 'nullable|string|max:255',
            'accent_color' => 'nullable|string|max:255',
            'plan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $name = $request->input('name');
        $subdomain = Str::slug($request->input('subdomain'));
        $publicId = $subdomain;

        $domainName = "{$subdomain}.rederevenda.com";

        // Start transaction
        DB::beginTransaction();

        try {
            // Insert Store
            $storeId = DB::table('stores')->insertGetId([
                'public_id' => $publicId,
                'name' => $name,
                'slug' => Str::slug($name),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Domain
            DB::table('store_domains')->insert([
                'store_id' => $storeId,
                'domain' => $domainName,
                'type' => 'internal',
                'is_primary' => true,
                'is_verified' => true,
                'verified_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Settings
            $settings = [
                [
                    'store_id' => $storeId,
                    'key' => 'logo_url',
                    'value' => 'https://api.rederevenda.com/images/default-logo.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'store_id' => $storeId,
                    'key' => 'whatsapp_number',
                    'value' => $request->input('whatsapp_number') ?? '5584999999999',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'store_id' => $storeId,
                    'key' => 'accent_color',
                    'value' => $request->input('accent_color') ?? '#4f46e5',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'store_id' => $storeId,
                    'key' => 'plan',
                    'value' => $request->input('plan') ?? 'mensal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('store_settings')->insert($settings);

            DB::commit();

            return response()->json([
                'message' => 'Tenant criado e provisionado com sucesso!',
                'store_id' => $storeId,
                'domain' => $domainName,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro interno ao provisionar tenant.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle a store status.
     */
    public function toggle(int $id): JsonResponse
    {
        $store = DB::table('stores')->where('id', $id)->first();

        if ($store === null) {
            return response()->json([
                'message' => 'Loja não encontrada.',
            ], 404);
        }

        $newStatus = $store->status === 'active' ? 'inactive' : 'active';

        DB::table('stores')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => "Status da loja alterado para {$newStatus} com sucesso!",
            'status' => $newStatus,
        ]);
    }
}
