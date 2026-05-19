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
        $normalized = [];

        // 1. Detect if it's the nested semantic payload format
        if (isset($filters['search']) || isset($filters['location']) || isset($filters['filters']) || isset($filters['sort']) || isset($filters['pagination'])) {
            
            // Search Text Query
            if (isset($filters['search']['term']) && $filters['search']['term'] !== '') {
                $normalized['q'] = $filters['search']['term'];
            }

            // Location Logic
            if (isset($filters['location'])) {
                $loc = $filters['location'];
                $mode = $loc['mode'] ?? 'city';

                if ($mode === 'city' && !empty($loc['city_id'])) {
                    $normalized['city_id'] = $loc['city_id'];
                } elseif ($mode === 'state' && !empty($loc['state_id'])) {
                    $normalized['state_id'] = $loc['state_id'];
                } elseif ($mode === 'radius' && !empty($loc['city_id']) && !empty($loc['radius_km'])) {
                    $normalized['city_id'] = $loc['city_id'];
                    $normalized['radius'] = $loc['radius_km'];
                }
            }

            // Custom Filters
            if (isset($filters['filters'])) {
                $f = $filters['filters'];
                if (!empty($f['brand_id'])) {
                    $normalized['brand_id'] = $f['brand_id'];
                }
                if (!empty($f['model_id'])) {
                    $normalized['vehicle_model_id'] = $f['model_id'];
                }
                if (isset($f['price']['min']) && $f['price']['min'] !== '') {
                    $normalized['price_min'] = $f['price']['min'];
                }
                if (isset($f['price']['max']) && $f['price']['max'] !== '') {
                    $normalized['price_max'] = $f['price']['max'];
                }
                if (isset($f['year']['min']) && $f['year']['min'] !== '') {
                    $normalized['year_min'] = $f['year']['min'];
                }
                if (isset($f['year']['max']) && $f['year']['max'] !== '') {
                    $normalized['year_max'] = $f['year']['max'];
                }
                if (isset($f['mileage']['max']) && $f['mileage']['max'] !== '') {
                    $normalized['mileage_max'] = $f['mileage']['max'];
                }
                if (isset($f['transmission']) && !empty($f['transmission'])) {
                    $normalized['transmission'] = $f['transmission'];
                }
                if (isset($f['fuel']) && !empty($f['fuel'])) {
                    $normalized['fuel'] = $f['fuel'];
                }
                if (isset($f['body_type']) && !empty($f['body_type'])) {
                    $normalized['body_type'] = $f['body_type'];
                }
            }

            // Custom Sort parameters
            if (isset($filters['sort'])) {
                $normalized['sort_field'] = $filters['sort']['field'] ?? 'created_at';
                $normalized['sort_direction'] = $filters['sort']['direction'] ?? 'desc';
            }

            // Custom Pagination
            if (isset($filters['pagination'])) {
                if (!empty($filters['pagination']['page'])) {
                    $normalized['page'] = $filters['pagination']['page'];
                    request()->merge(['page' => $filters['pagination']['page']]);
                }
                if (!empty($filters['pagination']['per_page'])) {
                    $normalized['per_page'] = $filters['pagination']['per_page'];
                }
            }

            // Preserve top-level properties
            if (isset($filters['store_id'])) {
                $normalized['store_id'] = $filters['store_id'];
            }
            if (isset($filters['status'])) {
                $normalized['status'] = $filters['status'];
            }
        } else {
            // It's the flat legacy query structure
            $normalized = $filters;
        }

        // 2. Build Query
        $query = Vehicle::query()
            ->with(['brand', 'model', 'city.state', 'store', 'images' => function ($q) {
                $q->orderBy('order', 'asc');
            }])
            ->where('vehicles.status', '=', $normalized['status'] ?? 'published');

        if (!empty($normalized['store_id'])) {
            $query->where('vehicles.store_id', '=', (int) $normalized['store_id']);
        }

        // Apply Text Search
        if (!empty($normalized['q'])) {
            $searchTerm = '%' . strtolower(trim((string) $normalized['q'])) . '%';
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
        if (!empty($normalized['brand_id'])) {
            $query->where('vehicles.brand_id', '=', (int) $normalized['brand_id']);
        }

        if (!empty($normalized['vehicle_model_id'])) {
            $query->where('vehicles.vehicle_model_id', '=', (int) $normalized['vehicle_model_id']);
        }

        // Dynamic city-to-coordinate lookup for radius search
        if (!empty($normalized['city_id']) && !empty($normalized['radius']) && !isset($normalized['lat'])) {
            $city = \App\Models\City::find($normalized['city_id']);
            if ($city && $city->latitude && $city->longitude) {
                $normalized['lat'] = $city->latitude;
                $normalized['lng'] = $city->longitude;
            }
        }

        // Apply Geo Coordinate Radius Search (Haversine Distance)
        $hasCoordinates = isset($normalized['lat'], $normalized['lng'], $normalized['radius']);

        if ($hasCoordinates) {
            $lat = (float) $normalized['lat'];
            $lng = (float) $normalized['lng'];
            $radius = (float) $normalized['radius']; // in Kilometers

            // Join cities table to access coordinates
            $query->join('cities', 'vehicles.city_id', '=', 'cities.id');

            // Haversine Distance Formula in Kilometers
            $haversine = "6371 * acos(cos(radians(?)) * cos(radians(cities.latitude)) * cos(radians(cities.longitude) - radians(?)) + sin(radians(?)) * sin(radians(cities.latitude)))";

            $query->selectRaw("vehicles.*, ({$haversine}) as distance", [$lat, $lng, $lat])
                  ->whereRaw("({$haversine}) <= ?", [$lat, $lng, $lat, $radius]);
        } else {
            // Apply simple City or State search if coordinates are not used
            if (!empty($normalized['city_id'])) {
                $query->where('vehicles.city_id', '=', (int) $normalized['city_id']);
            }

            if (!empty($normalized['state_id'])) {
                $query->whereHas('city', function (Builder $q) use ($normalized) {
                    $q->where('cities.state_id', '=', (int) $normalized['state_id']);
                });
            }
        }

        // Apply Pricing Filters
        if (isset($normalized['price_min'])) {
            $query->where('vehicles.price', '>=', (float) $normalized['price_min']);
        }

        if (isset($normalized['price_max'])) {
            $query->where('vehicles.price', '<=', (float) $normalized['price_max']);
        }

        // Apply Year Filters
        if (isset($normalized['year_min'])) {
            $query->where('vehicles.year_model', '>=', (int) $normalized['year_min']);
        }

        if (isset($normalized['year_max'])) {
            $query->where('vehicles.year_model', '<=', (int) $normalized['year_max']);
        }

        // Apply Mileage Filter
        if (isset($normalized['mileage_max'])) {
            $query->where('vehicles.mileage', '<=', (int) $normalized['mileage_max']);
        }

        // Apply Fuel & Transmission Filters (String or Array)
        if (!empty($normalized['transmission'])) {
            if (is_array($normalized['transmission'])) {
                $query->whereIn('vehicles.transmission', $normalized['transmission']);
            } else {
                $query->where('vehicles.transmission', '=', (string) $normalized['transmission']);
            }
        }

        if (!empty($normalized['fuel'])) {
            if (is_array($normalized['fuel'])) {
                $query->whereIn('vehicles.fuel', $normalized['fuel']);
            } else {
                $query->where('vehicles.fuel', '=', (string) $normalized['fuel']);
            }
        }

        if (!empty($normalized['body_type'])) {
            if (is_array($normalized['body_type'])) {
                $query->whereIn('vehicles.body_type', $normalized['body_type']);
            } else {
                $query->where('vehicles.body_type', '=', (string) $normalized['body_type']);
            }
        }

        // 3. Dynamic Sorting
        $sortField = $normalized['sort_field'] ?? null;
        $sortDir = strtolower($normalized['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sortField) {
            // Map common sort keys to columns
            $col = match ($sortField) {
                'price' => 'vehicles.price',
                'year', 'year_model' => 'vehicles.year_model',
                'mileage' => 'vehicles.mileage',
                'newest', 'created_at' => 'vehicles.created_at',
                default => 'vehicles.created_at'
            };
            $query->orderBy($col, $sortDir);
        } elseif ($hasCoordinates) {
            // Default sort by distance proximity if coordinates are queried
            $query->orderBy('distance', 'asc');
        } else {
            // Default sort by newest
            $query->orderBy('vehicles.created_at', 'desc');
        }

        $perPage = isset($normalized['per_page']) ? (int) $normalized['per_page'] : 15;

        return $query->paginate($perPage);
    }
}
