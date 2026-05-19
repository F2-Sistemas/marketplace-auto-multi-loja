<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    private VehicleSearchService $searchService;

    public function __construct(VehicleSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * List and search vehicles.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'q',
            'brand_id',
            'vehicle_model_id',
            'city_id',
            'state_id',
            'price_min',
            'price_max',
            'year_min',
            'year_max',
            'mileage_max',
            'transmission',
            'fuel',
            'lat',
            'lng',
            'radius',
            'per_page',
        ]);

        $paginator = $this->searchService->search($filters);

        return response()->json($paginator);
    }

    /**
     * Search vehicles with standard semantic payload.
     */
    public function search(Request $request): JsonResponse
    {
        $payload = $request->all();
        $paginator = $this->searchService->search($payload);

        return response()->json($paginator);
    }

    /**
     * Show vehicle details.
     */
    public function show(int $id): JsonResponse
    {
        $vehicle = Vehicle::with(['brand', 'model', 'city.state', 'store', 'images' => function ($q) {
            $q->orderBy('order', 'asc');
        }])->find($id);

        if ($vehicle === null) {
            return response()->json([
                'message' => 'Veículo não encontrado.',
            ], 404);
        }

        return response()->json($vehicle);
    }
}
