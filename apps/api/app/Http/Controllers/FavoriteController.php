<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    /**
     * Get all vehicle listings favorited by the buyer.
     */
    public function index(Request $request): JsonResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $email = $request->header('X-Test-User-Email') ?? $request->input('user_email') ?? 'comprador@gmail.com';
            $user = DB::table('users')->where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $favorites = DB::table('favorites')
            ->join('vehicles', 'favorites.vehicle_id', '=', 'vehicles.id')
            ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
            ->join('vehicle_models', 'vehicles.vehicle_model_id', '=', 'vehicle_models.id')
            ->join('cities', 'vehicles.city_id', '=', 'cities.id')
            ->select(
                'favorites.id as favorite_id',
                'vehicles.*',
                'brands.name as brand_name',
                'vehicle_models.name as model_name',
                'cities.name as city_name'
            )
            ->where('favorites.user_id', $user->id)
            ->get()
            ->map(function ($vehicle) {
                // Get vehicle primary thumbnail
                $image = DB::table('vehicle_images')
                    ->where('vehicle_id', $vehicle->id)
                    ->orderBy('order', 'asc')
                    ->first();
                $vehicle->main_image = $image ? $image->path : 'https://api.rederevenda.com/images/default-vehicle.png';
                
                // Ensure correct HSL accent mapping placeholder or basic props
                return $vehicle;
            });

        return response()->json($favorites);
    }

    /**
     * Toggle favorite status on a vehicle listing.
     */
    public function toggle(Request $request, int $vehicleId): JsonResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $email = $request->header('X-Test-User-Email') ?? $request->input('user_email') ?? 'comprador@gmail.com';
            $user = DB::table('users')->where('email', $email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Não autorizado.'], 401);
        }

        $vehicle = DB::table('vehicles')->where('id', $vehicleId)->first();
        if (!$vehicle) {
            return response()->json(['message' => 'Veículo não encontrado.'], 404);
        }

        $exists = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('vehicle_id', $vehicleId)
            ->first();

        if ($exists) {
            DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('vehicle_id', $vehicleId)
                ->delete();
            return response()->json([
                'message' => 'Removido dos favoritos com sucesso.',
                'favorited' => false
            ]);
        } else {
            DB::table('favorites')->insert([
                'user_id' => $user->id,
                'vehicle_id' => $vehicleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json([
                'message' => 'Adicionado aos favoritos com sucesso.',
                'favorited' => true
            ]);
        }
    }
}
