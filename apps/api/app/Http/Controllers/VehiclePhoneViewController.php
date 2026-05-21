<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehiclePhoneViewController extends Controller
{
    /**
     * Store a phone reveal event for a vehicle.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vehicle = Vehicle::with('store')->find($id);

        if ($vehicle === null) {
            return response()->json([
                'message' => 'Veículo não encontrado.',
            ], 404);
        }

        $phoneViewId = DB::table('vehicle_phone_views')->insertGetId([
            'vehicle_id' => $vehicle->id,
            'store_id' => $vehicle->store_id,
            'vehicle_title' => $vehicle->title,
            'vehicle_slug' => $vehicle->slug,
            'store_name' => $vehicle->store ? $vehicle->store->name : null,
            'viewer_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Visualização de telefone registrada com sucesso.',
            'phone_view_id' => $phoneViewId,
        ], 201);
    }
}
