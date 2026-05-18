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
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SitemapCompilationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test console sitemap command compiles XML correctly.
     */
    public function test_sitemap_command_generates_xml_on_disk(): void
    {
        // 1. Create a store, domain, and vehicle
        $store = Store::create(['public_id' => 's1', 'name' => 'Store 1', 'slug' => 's1', 'status' => 'active']);
        StoreDomain::create([
            'store_id' => $store->id,
            'domain' => 's1.app-loja.rederevenda.com',
            'type' => 'internal',
            'is_primary' => true,
            'is_verified' => true,
            'status' => 'active',
        ]);

        $brand = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Corolla', 'slug' => 'corolla']);
        $state = State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        $city = City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'slug' => 'sao-paulo']);

        app(TenantManager::class)->setStore($store);

        $vehicle = Vehicle::create([
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'city_id' => $city->id,
            'title' => 'Corolla S1',
            'slug' => 'corolla-s1',
            'price' => 85000.00,
            'year_manufacture' => 2018,
            'year_model' => 2019,
            'mileage' => 45000,
            'status' => 'published',
        ]);

        // Cleanup any existing sitemaps
        $directory = public_path('sitemaps');
        if (File::exists($directory)) {
            File::deleteDirectory($directory);
        }

        // 2. Call command synchronously (jobs will be processed instantly because queue driver is sync in tests)
        $this->artisan('sitemap:generate', ['--scope' => 'all'])
            ->assertExitCode(0);

        // 3. Verify files exist on disk
        $portalSitemapPath = $directory . '/sitemap-portal.xml';
        $storeSitemapPath = $directory . "/sitemap-store-{$store->id}.xml";

        $this->assertTrue(File::exists($portalSitemapPath));
        $this->assertTrue(File::exists($storeSitemapPath));

        // 4. Verify content contains vehicle details
        $portalContent = File::get($portalSitemapPath);
        $this->assertStringContainsString('https://rederevenda.com/veiculos/corolla-s1', $portalContent);

        $storeContent = File::get($storeSitemapPath);
        $this->assertStringContainsString("https://s1.app-loja.rederevenda.com/veiculos/corolla-s1", $storeContent);
    }
}
