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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleSearchPayloadTest extends TestCase
{
    use RefreshDatabase;

    private Store $store;
    private Brand $toyota;
    private Brand $honda;
    private VehicleModel $corolla;
    private VehicleModel $civic;
    private State $sp;
    private State $rn;
    private City $saoPaulo;
    private City $campinas;
    private City $natal;

    protected function setUp(): void
    {
        parent::setUp();
        app(TenantManager::class)->clear();

        // 1. Seed Stores
        $this->store = Store::create([
            'public_id' => 'sp-veiculos',
            'name' => 'SP Veículos',
            'slug' => 'sp-veiculos',
            'status' => 'active'
        ]);

        // 2. Seed Brands & Models
        $this->toyota = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $this->honda = Brand::create(['name' => 'Honda', 'slug' => 'honda']);

        $this->corolla = VehicleModel::create(['brand_id' => $this->toyota->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $this->civic = VehicleModel::create(['brand_id' => $this->honda->id, 'name' => 'Civic', 'slug' => 'civic']);

        // 3. Seed States & Cities (São Paulo Coordinates vs Campinas vs Natal)
        $this->sp = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $this->rn = State::create(['name' => 'Rio Grande do Norte', 'uf' => 'RN']);

        // São Paulo (Capital): Lat -23.55052, Lng -46.633308
        $this->saoPaulo = City::create([
            'state_id' => $this->sp->id,
            'name' => 'São Paulo',
            'slug' => 'sao-paulo',
            'latitude' => -23.550520,
            'longitude' => -46.633308
        ]);

        // Campinas: (~90km from SP Capital): Lat -22.9099, Lng -47.0626
        $this->campinas = City::create([
            'state_id' => $this->sp->id,
            'name' => 'Campinas',
            'slug' => 'campinas',
            'latitude' => -22.909900,
            'longitude' => -47.062600
        ]);

        // Natal (Capital of RN): (~2200km from SP Capital): Lat -5.779257, Lng -35.200916
        $this->natal = City::create([
            'state_id' => $this->rn->id,
            'name' => 'Natal',
            'slug' => 'natal',
            'latitude' => -5.779257,
            'longitude' => -35.200916
        ]);

        // 4. Seed Vehicles
        // Vehicle 1: SP Corolla
        Vehicle::create([
            'store_id' => $this->store->id,
            'brand_id' => $this->toyota->id,
            'vehicle_model_id' => $this->corolla->id,
            'city_id' => $this->saoPaulo->id,
            'title' => 'Toyota Corolla Altis Premium',
            'slug' => 'toyota-corolla-altis-premium',
            'version' => '2.0 Flex CVT',
            'year_manufacture' => 2022,
            'year_model' => 2023,
            'mileage' => 15000,
            'price' => 125000.00,
            'transmission' => 'automatic',
            'fuel' => 'flex',
            'status' => 'published',
            'created_at' => now()->subDays(5)
        ]);

        // Vehicle 2: Campinas Civic
        Vehicle::create([
            'store_id' => $this->store->id,
            'brand_id' => $this->honda->id,
            'vehicle_model_id' => $this->civic->id,
            'city_id' => $this->campinas->id,
            'title' => 'Honda Civic Touring',
            'slug' => 'honda-civic-touring',
            'version' => '1.5 Turbo Gasolina',
            'year_manufacture' => 2021,
            'year_model' => 2021,
            'mileage' => 45000,
            'price' => 140000.00,
            'transmission' => 'automatic',
            'fuel' => 'gasoline',
            'status' => 'published',
            'created_at' => now()->subDays(2)
        ]);

        // Vehicle 3: Natal Corolla
        Vehicle::create([
            'store_id' => $this->store->id,
            'brand_id' => $this->toyota->id,
            'vehicle_model_id' => $this->corolla->id,
            'city_id' => $this->natal->id,
            'title' => 'Toyota Corolla GLi',
            'slug' => 'toyota-corolla-gli',
            'version' => '2.0 Flex CVT',
            'year_manufacture' => 2019,
            'year_model' => 2020,
            'mileage' => 80000,
            'price' => 95000.00,
            'transmission' => 'automatic',
            'fuel' => 'flex',
            'status' => 'published',
            'created_at' => now()->subDays(10)
        ]);
    }

    /**
     * Test semantic search with text query.
     */
    public function testSearchByTerm(): void
    {
        $payload = [
            'search' => ['term' => 'Touring']
        ];

        $response = $this->postJson('/api/vehicles/search', $payload);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Honda Civic Touring');
    }

    /**
     * Test semantic search filtering by single City.
     */
    public function testSearchByCityMode(): void
    {
        $payload = [
            'location' => [
                'mode' => 'city',
                'city_id' => $this->natal->id
            ]
        ];

        $response = $this->postJson('/api/vehicles/search', $payload);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Toyota Corolla GLi');
    }

    /**
     * Test semantic search filtering by entire State.
     */
    public function testSearchByStateMode(): void
    {
        $payload = [
            'location' => [
                'mode' => 'state',
                'state_id' => $this->sp->id
            ]
        ];

        // Should return vehicles in Sao Paulo capital and Campinas (total 2)
        $response = $this->postJson('/api/vehicles/search', $payload);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test location radius matching (Haversine distance dynamic resolving).
     */
    public function testSearchByLocationRadiusCampinasFromSP(): void
    {
        // Campinas is ~90km away from SP capital.
        // If we query SP capital with 50km radius, Campinas should NOT be included.
        $payloadNear = [
            'location' => [
                'mode' => 'radius',
                'city_id' => $this->saoPaulo->id,
                'radius_km' => 50
            ]
        ];

        $responseNear = $this->postJson('/api/vehicles/search', $payloadNear);
        $responseNear->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Toyota Corolla Altis Premium');

        // If we query SP capital with 100km radius, Campinas SHOULD be included.
        $payloadFar = [
            'location' => [
                'mode' => 'radius',
                'city_id' => $this->saoPaulo->id,
                'radius_km' => 100
            ]
        ];

        $responseFar = $this->postJson('/api/vehicles/search', $payloadFar);
        $responseFar->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test filters containing transmission, fuel array and price bounds.
     */
    public function testSearchComplexFiltersAndPricing(): void
    {
        $payload = [
            'filters' => [
                'price' => ['min' => 100000, 'max' => 130000],
                'fuel' => ['flex'],
                'transmission' => ['automatic']
            ]
        ];

        $response = $this->postJson('/api/vehicles/search', $payload);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Toyota Corolla Altis Premium');
    }

    /**
     * Test sorting dynamically on columns.
     */
    public function testSearchSortByPriceAscending(): void
    {
        $payload = [
            'sort' => [
                'field' => 'price',
                'direction' => 'asc'
            ]
        ];

        $response = $this->postJson('/api/vehicles/search', $payload);

        // Expected prices order: 95000 -> 125000 -> 140000
        $response->assertStatus(200)
            ->assertJsonPath('data.0.price', 95000)
            ->assertJsonPath('data.1.price', 125000)
            ->assertJsonPath('data.2.price', 140000);
    }
}
