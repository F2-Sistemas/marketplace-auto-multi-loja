<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InterestListController extends Controller
{
    /**
     * Resolve the current buyer user using auth or the testing header.
     */
    private function resolveUser(Request $request): ?object
    {
        if (auth()->check()) {
            return auth()->user();
        }

        $email = $request->header('X-Test-User-Email') ?? $request->input('user_email') ?? 'comprador@gmail.com';

        return DB::table('users')->where('email', $email)->first();
    }

    /**
     * Get the current user's interest lists.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if ($user === null) {
            return response()->json([
                'message' => 'Não autorizado.',
            ], 401);
        }

        $lists = DB::table('interest_lists')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (object $list): object {
                $count = DB::table('interest_list_items')
                    ->where('interest_list_id', $list->id)
                    ->count();

                $vehicles = DB::table('interest_list_items')
                    ->join('vehicles', 'interest_list_items.vehicle_id', '=', 'vehicles.id')
                    ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                    ->join('vehicle_models', 'vehicles.vehicle_model_id', '=', 'vehicle_models.id')
                    ->leftJoin('vehicle_images', function ($join): void {
                        $join->on('vehicle_images.vehicle_id', '=', 'vehicles.id')
                            ->where('vehicle_images.is_featured', '=', true);
                    })
                    ->where('interest_list_items.interest_list_id', $list->id)
                    ->select(
                        'vehicles.id',
                        'vehicles.title',
                        'vehicles.slug',
                        'vehicles.version',
                        'vehicles.price',
                        'vehicles.year_manufacture',
                        'vehicles.year_model',
                        'vehicles.mileage',
                        'vehicles.transmission',
                        'vehicles.fuel',
                        'brands.name as brand_name',
                        'vehicle_models.name as model_name',
                        DB::raw('COALESCE(vehicle_images.path, "https://api.rederevenda.com/images/default-vehicle.png") as main_image')
                    )
                    ->orderBy('interest_list_items.created_at', 'asc')
                    ->limit(3)
                    ->get()
                    ->map(function (object $vehicle): object {
                        return $vehicle;
                    });

                return (object) [
                    'id' => $list->id,
                    'name' => $list->name,
                    'slug' => $list->slug,
                    'description' => $list->description,
                    'is_public' => (bool) $list->is_public,
                    'public_url' => $list->is_public ? '/listas/' . $list->slug : null,
                    'items_count' => $count,
                    'vehicles' => $vehicles,
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at,
                ];
            });

        return response()->json($lists);
    }

    /**
     * Store a new interest list.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if ($user === null) {
            return response()->json([
                'message' => 'Não autorizado.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $name = trim((string) $request->input('name'));
        $slugBase = Str::slug($name);
        $slug = $slugBase . '-' . Str::lower(Str::random(6));
        $isPublic = filter_var($request->input('is_public', false), FILTER_VALIDATE_BOOL);

        $listId = DB::table('interest_lists')->insertGetId([
            'user_id' => $user->id,
            'name' => $name,
            'slug' => $slug,
            'description' => $request->input('description'),
            'is_public' => $isPublic,
            'public_token' => $isPublic ? Str::random(48) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Lista criada com sucesso.',
            'list_id' => $listId,
        ], 201);
    }

    /**
     * Update an interest list owned by the current user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if ($user === null) {
            return response()->json([
                'message' => 'Não autorizado.',
            ], 401);
        }

        $list = DB::table('interest_lists')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($list === null) {
            return response()->json([
                'message' => 'Lista não encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $isPublic = filter_var($request->input('is_public', false), FILTER_VALIDATE_BOOL);

        DB::table('interest_lists')
            ->where('id', $id)
            ->update([
                'name' => trim((string) $request->input('name')),
                'description' => $request->input('description'),
                'is_public' => $isPublic,
                'public_token' => $isPublic && empty($list->public_token) ? Str::random(48) : $list->public_token,
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Lista atualizada com sucesso.',
        ]);
    }

    /**
     * Add a vehicle to a list owned by the current user.
     */
    public function addVehicle(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if ($user === null) {
            return response()->json([
                'message' => 'Não autorizado.',
            ], 401);
        }

        $list = DB::table('interest_lists')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($list === null) {
            return response()->json([
                'message' => 'Lista não encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vehicleId = (int) $request->input('vehicle_id');

        $exists = DB::table('interest_list_items')
            ->where('interest_list_id', $id)
            ->where('vehicle_id', $vehicleId)
            ->exists();

        if (! $exists) {
            DB::table('interest_list_items')->insert([
                'interest_list_id' => $id,
                'vehicle_id' => $vehicleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Veículo adicionado à lista com sucesso.',
            'added' => ! $exists,
        ]);
    }

    /**
     * Show a public interest list by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $list = DB::table('interest_lists')->where('slug', $slug)->first();

        if ($list === null || ! (bool) $list->is_public) {
            return response()->json([
                'message' => 'lista inexistente ou privada',
            ], 404);
        }

        $vehicles = DB::table('interest_list_items')
            ->join('vehicles', 'interest_list_items.vehicle_id', '=', 'vehicles.id')
            ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
            ->join('vehicle_models', 'vehicles.vehicle_model_id', '=', 'vehicle_models.id')
            ->join('cities', 'vehicles.city_id', '=', 'cities.id')
            ->leftJoin('vehicle_images', function ($join): void {
                $join->on('vehicle_images.vehicle_id', '=', 'vehicles.id')
                    ->where('vehicle_images.is_featured', '=', true);
            })
            ->where('interest_list_items.interest_list_id', $list->id)
            ->select(
                'vehicles.id',
                'vehicles.title',
                'vehicles.slug',
                'vehicles.version',
                'vehicles.price',
                'vehicles.year_manufacture',
                'vehicles.year_model',
                'vehicles.mileage',
                'vehicles.transmission',
                'vehicles.fuel',
                'vehicles.color',
                'brands.name as brand_name',
                'vehicle_models.name as model_name',
                'cities.name as city_name',
                DB::raw('COALESCE(vehicle_images.path, "https://api.rederevenda.com/images/default-vehicle.png") as main_image')
            )
            ->orderBy('interest_list_items.created_at', 'asc')
            ->get();

        return response()->json([
            'list' => [
                'id' => $list->id,
                'name' => $list->name,
                'slug' => $list->slug,
                'description' => $list->description,
                'is_public' => (bool) $list->is_public,
                'items_count' => $vehicles->count(),
                'public_url' => '/listas/' . $list->slug,
                'created_at' => $list->created_at,
            ],
            'vehicles' => $vehicles,
        ]);
    }
}
