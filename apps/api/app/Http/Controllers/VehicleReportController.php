<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleReportController extends Controller
{
    /**
     * Store a new vehicle report.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:incorrect_information,suspected_fraud,duplicate_listing,offensive_content,already_sold,other',
            'details' => 'nullable|string|max:2000',
            'reporter_name' => 'nullable|string|max:120',
            'reporter_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicle = Vehicle::with('store')->find($id);
        if ($vehicle === null) {
            return response()->json(['message' => 'Veículo não encontrado.'], 404);
        }

        $reason = (string) $request->input('reason');
        $details = $request->input('details');
        $reporterName = $request->input('reporter_name');
        $reporterEmail = $request->input('reporter_email');

        $reportId = DB::table('vehicle_reports')->insertGetId([
            'vehicle_id' => $vehicle->id,
            'store_id' => $vehicle->store_id,
            'vehicle_title' => $vehicle->title,
            'vehicle_slug' => $vehicle->slug,
            'store_name' => $vehicle->store ? $vehicle->store->name : null,
            'reason' => $reason,
            'details' => $details,
            'status' => 'pending',
            'reporter_name' => $reporterName,
            'reporter_email' => $reporterEmail,
            'reporter_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Denúncia enviada com sucesso.',
            'report_id' => $reportId,
        ], 201);
    }
}
