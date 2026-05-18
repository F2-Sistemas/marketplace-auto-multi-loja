<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\City;
use App\Models\State;
use App\Models\Store;
use App\Models\StoreDomain;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RestApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(TenantManager::class)->clear();
    }

    /**
     * Test tenant endpoint in Portal context.
     */
    public function testTenantEndpointReturnsPortalContext(): void
    {
        $response = $this->getJson('/api/tenant', [
            'X-Store-Host' => 'rederevenda.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_portal' => true,
                'name' => 'AutoHub Central'
            ]);
    }

    /**
     * Test tenant endpoint in Store/Tenant context.
     */
    public function testTenantEndpointReturnsStoreContext(): void
    {
        $store = Store::create([
            'public_id' => 'natal-motors',
            'name' => 'Natal Motors',
            'slug' => 'natal-motors',
            'status' => 'active'
        ]);

        StoreDomain::create([
            'store_id' => $store->id,
            'domain' => 'natal-motors.rederevenda.com',
            'type' => 'internal',
            'is_primary' => true,
            'is_verified' => true,
            'status' => 'active'
        ]);

        DB::table('store_settings')->insert([
            ['store_id' => $store->id, 'key' => 'logo_url', 'value' => 'logo.png', 'created_at' => now(), 'updated_at' => now()],
            ['store_id' => $store->id, 'key' => 'accent_color', 'value' => '#ff0000', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->getJson('/api/tenant', [
            'X-Store-Host' => 'natal-motors.rederevenda.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_portal' => false,
                'name' => 'Natal Motors',
                'settings' => [
                    'logo_url' => 'logo.png',
                    'accent_color' => '#ff0000'
                ]
            ]);
    }

    /**
     * Test vehicle list, search and detail specs.
     */
    public function testVehicleEndpointListingsAndDetails(): void
    {
        $store = Store::create([
            'public_id' => 'sp-veiculos',
            'name' => 'SP Veículos',
            'slug' => 'sp-veiculos',
            'status' => 'active'
        ]);

        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $state = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $city = City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'slug' => 'sao-paulo', 'latitude' => -23.5505, 'longitude' => -46.6333]);

        $vehicle = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Toyota Corolla Altis',
            'slug' => 'toyota-corolla-altis',
            'version' => '2.0 Flex',
            'year_manufacture' => 2022,
            'year_model' => 2022,
            'mileage' => 25000,
            'price' => 115000,
            'transmission' => 'automatic',
            'fuel' => 'flex',
            'color' => 'Prata',
            'status' => 'published'
        ]);

        // 1. Fetch public vehicles
        $response = $this->getJson('/api/vehicles');
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Toyota Corolla Altis']);

        // 2. Fetch specific details
        $detailResponse = $this->getJson("/api/vehicles/{$vehicle->id}");
        $detailResponse->assertStatus(200)
            ->assertJsonPath('title', 'Toyota Corolla Altis');
    }

    /**
     * Test client lead/proposal submission.
     */
    public function testLeadSubmissionPersistence(): void
    {
        $store = Store::create([
            'public_id' => 'sp-veiculos',
            'name' => 'SP Veículos',
            'slug' => 'sp-veiculos',
            'status' => 'active'
        ]);

        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $state = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $city = City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'slug' => 'sao-paulo-lead', 'latitude' => -23.5505, 'longitude' => -46.6333]);

        $vehicle = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Toyota Corolla Altis',
            'slug' => 'toyota-corolla-altis-lead',
            'version' => '2.0 Flex',
            'year_manufacture' => 2022,
            'year_model' => 2022,
            'mileage' => 25000,
            'price' => 115000,
            'transmission' => 'automatic',
            'fuel' => 'flex',
            'color' => 'Prata',
            'status' => 'published'
        ]);

        // Submit proposal
        $response = $this->postJson('/api/leads', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'phone' => '5511999999999',
            'message' => 'Gostaria de fazer uma proposta!',
            'vehicle_id' => $vehicle->id
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Proposta enviada com sucesso!']);

        $this->assertDatabaseHas('leads', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'vehicle_id' => $vehicle->id
        ]);
    }

    /**
     * Test central backoffice stores listing and wizard creation.
     */
    public function testBackofficeStoreListingAndProvisioning(): void
    {
        // 1. Create a store
        $store = Store::create([
            'public_id' => 'natal-motors',
            'name' => 'Natal Motors',
            'slug' => 'natal-motors',
            'status' => 'active'
        ]);

        // 2. Fetch admin store listing
        $response = $this->getJson('/api/admin/stores');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Natal Motors']);

        // 3. Provision a new tenant via Wizard
        $provisionResponse = $this->postJson('/api/admin/stores', [
            'name' => 'Euro Motors',
            'subdomain' => 'euro-motors',
            'whatsapp_number' => '5584888888888',
            'accent_color' => '#0000ff',
            'plan' => 'anual'
        ]);

        $provisionResponse->assertStatus(201)
            ->assertJsonFragment(['message' => 'Tenant criado e provisionado com sucesso!']);

        $this->assertDatabaseHas('stores', [
            'name' => 'Euro Motors',
            'public_id' => 'euro-motors'
        ]);

        $this->assertDatabaseHas('store_domains', [
            'domain' => 'euro-motors.rederevenda.com'
        ]);
    }
}
