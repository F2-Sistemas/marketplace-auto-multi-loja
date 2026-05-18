<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Store a new lead/proposal.
     */
    public function store(Request $request, TenantManager $tenantManager): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'nullable|string',
            'vehicle_id' => 'required|integer',
            'store_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $store = $tenantManager->getStore();
        $storeId = $store ? $store->id : $request->input('store_id');

        // If still no store, resolve store from vehicle
        if ($storeId === null) {
            $vehicle = DB::table('vehicles')->where('id', $request->input('vehicle_id'))->first();

            if ($vehicle === null) {
                return response()->json([
                    'message' => 'Veículo não encontrado.',
                ], 404);
            }
            $storeId = $vehicle->store_id;
        }

        // Insert lead
        $leadId = DB::table('leads')->insertGetId([
            'store_id' => $storeId,
            'vehicle_id' => $request->input('vehicle_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Proposta enviada com sucesso!',
            'lead_id' => $leadId,
        ], 201);
    }
}
