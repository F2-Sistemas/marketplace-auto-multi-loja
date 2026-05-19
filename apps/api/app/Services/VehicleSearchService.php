<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleSearchService
{
    /**
     * Search vehicles based on given filters.
     *
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Vehicle::query()
            ->with(['brand', 'model', 'city.state', 'store', 'images' => function ($q) {
                $q->orderBy('order', 'asc');
            }])
            ->where('vehicles.status', '=', $filters['status'] ?? 'published');

        // Apply Text Search
        if (!empty($filters['q'])) {
            $searchTerm = '%' . strtolower(trim((string) $filters['q'])) . '%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereRaw('LOWER(vehicles.title) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(vehicles.version) LIKE ?', [$searchTerm])
                  ->orWhereHas('brand', function (Builder $b) use ($searchTerm) {
                      $b->whereRaw('LOWER(brands.name) LIKE ?', [$searchTerm]);
                  })
                  ->orWhereHas('model', function (Builder $m) use ($searchTerm) {
                      $m->whereRaw('LOWER(vehicle_models.name) LIKE ?', [$searchTerm]);
                  });
            });
        }

        // Apply Relations Filters
        if (!empty($filters['brand_id'])) {
            $query->where('vehicles.brand_id', '=', (int) $filters['brand_id']);
        }

        if (!empty($filters['vehicle_model_id'])) {
            $query->where('vehicles.vehicle_model_id', '=', (int) $filters['vehicle_model_id']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('vehicles.city_id', '=', (int) $filters['city_id']);
        }

        if (!empty($filters['state_id'])) {
            $query->whereHas('city', function (Builder $q) use ($filters) {
                $q->where('cities.state_id', '=', (int) $filters['state_id']);
            });
        }

        // Apply Pricing Filters
        if (isset($filters['price_min'])) {
            $query->where('vehicles.price', '>=', (float) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('vehicles.price', '<=', (float) $filters['price_max']);
        }

        // Apply Year Filters
        if (isset($filters['year_min'])) {
            $query->where('vehicles.year_model', '>=', (int) $filters['year_min']);
        }

        if (isset($filters['year_max'])) {
            $query->where('vehicles.year_model', '<=', (int) $filters['year_max']);
        }

        // Apply Mileage Filter
        if (isset($filters['mileage_max'])) {
            $query->where('vehicles.mileage', '<=', (int) $filters['mileage_max']);
        }

        // Apply Fuel & Transmission Filters
        if (!empty($filters['transmission'])) {
            $query->where('vehicles.transmission', '=', (string) $filters['transmission']);
        }

        if (!empty($filters['fuel'])) {
            $query->where('vehicles.fuel', '=', (string) $filters['fuel']);
        }

        // Apply Geo Coordinate Radius Search (Haversine Distance)
        $hasCoordinates = isset($filters['lat'], $filters['lng'], $filters['radius']);

        if ($hasCoordinates) {
            $lat = (float) $filters['lat'];
            $lng = (float) $filters['lng'];
            $radius = (float) $filters['radius']; // in Kilometers

            // Join cities table to access coordinates
            $query->join('cities', 'vehicles.city_id', '=', 'cities.id');

            // Haversine Distance Formula in Kilometers
            $haversine = "6371 * acos(cos(radians(?)) * cos(radians(cities.latitude)) * cos(radians(cities.longitude) - radians(?)) + sin(radians(?)) * sin(radians(cities.latitude)))";

            $query->selectRaw("vehicles.*, ({$haversine}) as distance", [$lat, $lng, $lat])
                  ->whereRaw("({$haversine}) <= ?", [$lat, $lng, $lat, $radius])
                  ->orderBy('distance', 'asc');
        }

        // Fallback Order if no coordinates
        if (!$hasCoordinates) {
            $query->orderBy('vehicles.created_at', 'desc');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
    }
}
