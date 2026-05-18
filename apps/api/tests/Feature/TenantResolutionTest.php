<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Store;
use App\Models\StoreDomain;
use App\Services\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test dynamic tenant resolution by request host header.
     */
    public function testResolvesTenantFromRequestHost(): void
    {
        // 1. Create a store and internal domain
        $store = Store::create([
            'public_id' => 'natal-motors',
            'name' => 'Natal Motors',
            'slug' => 'natal-motors',
            'status' => 'active',
        ]);

        StoreDomain::create([
            'store_id' => $store->id,
            'domain' => 'natalmotors.app-loja.rederevenda.com',
            'type' => 'internal',
            'is_primary' => true,
            'is_verified' => true,
            'status' => 'active',
        ]);

        // 2. Clear tenant context
        app(TenantManager::class)->clear();

        // 3. Make request with tenant host header
        $response = $this->get('/up', [
            'X-Store-Host' => 'natalmotors.app-loja.rederevenda.com'
        ]);

        // 4. Verify tenant resolved successfully in TenantManager
        $tenantManager = app(TenantManager::class);
        $this->assertTrue($tenantManager->hasStore());
        $this->assertEquals($store->id, $tenantManager->getStoreId());
        $this->assertEquals('Natal Motors', $tenantManager->getStore()->name);
    }

    /**
     * Test dynamic tenant resolution cache.
     */
    public function testCachesTenantDomainResolutions(): void
    {
        $store = Store::create([
            'public_id' => 'natal-motors',
            'name' => 'Natal Motors',
            'slug' => 'natal-motors',
            'status' => 'active',
        ]);

        StoreDomain::create([
            'store_id' => $store->id,
            'domain' => 'natalmotors.app-loja.rederevenda.com',
            'type' => 'internal',
            'is_primary' => true,
            'is_verified' => true,
            'status' => 'active',
        ]);

        $host = 'natalmotors.app-loja.rederevenda.com';

        // Ensure key is not in cache yet
        Cache::forget("store:domain:{$host}");

        // Perform request to trigger caching
        $this->get('/up', ['X-Store-Host' => $host]);

        // Verify domain resolved ID was cached
        $this->assertEquals($store->id, Cache::get("store:domain:{$host}"));
    }

    /**
     * Test that technical portal domains are skipped for tenant resolution.
     */
    public function testSkipsTenantResolutionForPortalDomains(): void
    {
        app(TenantManager::class)->clear();

        // Perform request with portal host
        $this->get('/up', ['X-Store-Host' => 'api.rederevenda.com']);

        // Verify TenantManager remains empty
        $this->assertFalse(app(TenantManager::class)->hasStore());
    }
}
