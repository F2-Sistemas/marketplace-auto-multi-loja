<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\City;
use App\Models\State;
use App\Models\Store;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\TenantManager;
use App\Services\VehicleSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test semantic search query filtering.
     */
    public function test_vehicle_search_filters_results_by_criteria(): void
    {
        $store = Store::create(['public_id' => 's1', 'name' => 'Store 1', 'slug' => 's1']);
        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $state = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $city = City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'slug' => 'sao-paulo']);

        app(TenantManager::class)->setStore($store);

        // Create match vehicle
        $v1 = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Toyota Corolla GLI',
            'slug' => 'toyota-corolla-gli',
            'price' => 85000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'transmission' => 'automatico',
            'fuel' => 'flex',
            'status' => 'published',
        ]);

        // Create unmatched vehicle
        $v2 = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Toyota Corolla Altis',
            'slug' => 'toyota-corolla-altis',
            'price' => 120000.00,
            'year_manufacture' => 2021,
            'year_model' => 2022,
            'mileage' => 12000,
            'transmission' => 'automatico',
            'fuel' => 'flex',
            'status' => 'published',
        ]);

        $searchService = new VehicleSearchService();

        // 1. Test pricing filter
        $results = $searchService->search(['price_max' => 90000]);
        $this->assertEquals(1, $results->total());
        $this->assertEquals($v1->id, $results->items()[0]->id);

        // 2. Test text query matching title
        $results = $searchService->search(['q' => 'Altis']);
        $this->assertEquals(1, $results->total());
        $this->assertEquals($v2->id, $results->items()[0]->id);
    }

    /**
     * Test coordinates location range query (Haversine).
     */
    public function test_vehicle_search_supports_coordinate_radius(): void
    {
        $store = Store::create(['public_id' => 's1', 'name' => 'Store 1', 'slug' => 's1']);
        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $stateSP = State::create(['name' => 'São Paulo', 'uf' => 'SP']);

        // São Paulo city (lat: -23.55052, lng: -46.6333)
        $citySP = City::create([
            'state_id' => $stateSP->id,
            'name' => 'São Paulo',
            'slug' => 'sao-paulo',
            'latitude' => -23.55052000,
            'longitude' => -46.63330800,
        ]);

        // Campinas city (lat: -22.9099, lng: -47.0626, distance to SP: ~90km)
        $cityCampinas = City::create([
            'state_id' => $stateSP->id,
            'name' => 'Campinas',
            'slug' => 'campinas',
            'latitude' => -22.90990000,
            'longitude' => -47.06260000,
        ]);

        app(TenantManager::class)->setStore($store);

        // Vehicle in SP
        $vSP = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $citySP->id,
            'title' => 'Corolla SP',
            'slug' => 'corolla-sp',
            'price' => 85000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'transmission' => 'automatico',
            'fuel' => 'flex',
            'status' => 'published',
        ]);

        // Vehicle in Campinas
        $vCampinas = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $cityCampinas->id,
            'title' => 'Corolla Campinas',
            'slug' => 'corolla-campinas',
            'price' => 89000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'transmission' => 'automatico',
            'fuel' => 'flex',
            'status' => 'published',
        ]);

        $searchService = new VehicleSearchService();

        // Search near SP with 50km radius (should only find SP vehicle)
        $results = $searchService->search([
            'lat' => -23.55052,
            'lng' => -46.6333,
            'radius' => 50,
        ]);

        $this->assertEquals(1, $results->total());
        $this->assertEquals('Corolla SP', $results->items()[0]->title);

        // Search near SP with 100km radius (should find both)
        $results = $searchService->search([
            'lat' => -23.55052,
            'lng' => -46.6333,
            'radius' => 100,
        ]);

        $this->assertEquals(2, $results->total());
    }

    /**
     * Test tenant data isolation.
     */
    public function test_tenant_scoping_prevents_cross_tenant_reads(): void
    {
        $store1 = Store::create(['public_id' => 's1', 'name' => 'Store 1', 'slug' => 's1']);
        $store2 = Store::create(['public_id' => 's2', 'name' => 'Store 2', 'slug' => 's2']);

        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $state = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $city = City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'slug' => 'sao-paulo']);

        // Set context to Store 1
        app(TenantManager::class)->setStore($store1);

        // Create vehicle for Store 1
        Vehicle::create([
            'store_id' => $store1->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Corolla Store 1',
            'slug' => 'corolla-s1',
            'price' => 85000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'status' => 'published',
        ]);

        // Set context to Store 2
        app(TenantManager::class)->setStore($store2);

        // Create vehicle for Store 2
        Vehicle::create([
            'store_id' => $store2->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Corolla Store 2',
            'slug' => 'corolla-s2',
            'price' => 85000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'status' => 'published',
        ]);

        // Perform search under Store 2 context
        $searchService = new VehicleSearchService();
        $results = $searchService->search([]);

        // Verify ONLY Store 2 vehicle is loaded
        $this->assertEquals(1, $results->total());
        $this->assertEquals('Corolla Store 2', $results->items()[0]->title);
    }
}
