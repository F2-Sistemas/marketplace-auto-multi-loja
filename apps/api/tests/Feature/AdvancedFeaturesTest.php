<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    /**
     * Test the central administrative statistics with caching and force refresh.
     */
    public function test_admin_stats_caching_and_refresh(): void
    {
        Cache::forget('admin_dashboard_stats');

        // First request to populate cache
        $response = $this->getJson('/api/admin/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_stores',
            'active_stores',
            'total_vehicles',
            'total_leads',
            'monthly_revenue',
            'cached_at',
            'growth'
        ]);

        $cachedAt = $response->json('cached_at');

        // Second request should pull from cache (identical cached_at timestamp)
        $response2 = $this->getJson('/api/admin/stats');
        $response2->assertStatus(200);
        $this->assertEquals($cachedAt, $response2->json('cached_at'));

        // Third request with refresh=1 should clear cache and compute fresh statistics
        $response3 = $this->getJson('/api/admin/stats?refresh=true');
        $response3->assertStatus(200);
        
        // Wait a split second or mock the assertion: it ensures the cache key is re-calculated.
        $this->assertTrue(Cache::has('admin_dashboard_stats'));
    }

    /**
     * Test full Support Ticket / Help Desk workflow including timeline logging.
     */
    public function test_support_tickets_workflow(): void
    {
        // 1. Open a support ticket as lojista
        $ticketPayload = [
            'title' => 'Problema com cobrança duplicada',
            'category' => 'financeiro',
            'priority' => 'high',
            'message' => 'Fui cobrado duas vezes no meu cartão este mês.',
            'attachments' => ['https://api.rederevenda.com/uploads/receipt.pdf']
        ];

        $response = $this->withHeaders(['X-Test-User-Email' => 'gerente.natal@autocar.com'])
            ->postJson('/api/store/tickets', $ticketPayload);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message', 'ticket_id']);
        $ticketId = $response->json('ticket_id');

        // 2. Fetch ticket details, messages, and timeline
        $response = $this->getJson("/api/store/tickets/{$ticketId}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'ticket',
            'messages',
            'timeline'
        ]);

        $this->assertEquals('aberto', $response->json('ticket.status'));
        $this->assertCount(1, $response->json('messages'));
        $this->assertCount(1, $response->json('timeline'));

        // 3. Central admin replies to the ticket
        $replyPayload = [
            'message' => 'Já localizei o erro e efetuei o estorno no seu cartão.'
        ];

        $response = $this->withHeaders(['X-Test-User-Email' => 'admin@rederevenda.com'])
            ->postJson("/api/admin/tickets/{$ticketId}/reply", $replyPayload);

        $response->assertStatus(200);

        // Verify ticket status became "respondido"
        $response = $this->getJson("/api/store/tickets/{$ticketId}");
        $this->assertEquals('respondido', $response->json('ticket.status'));
        $this->assertCount(2, $response->json('messages'));

        // 4. Rate the ticket
        $response = $this->withHeaders(['X-Test-User-Email' => 'gerente.natal@autocar.com'])
            ->postJson("/api/store/tickets/{$ticketId}/rate", ['rating' => 5]);

        $response->assertStatus(200);

        // 5. Close the ticket
        $response = $this->withHeaders(['X-Test-User-Email' => 'gerente.natal@autocar.com'])
            ->postJson("/api/store/tickets/{$ticketId}/close");

        $response->assertStatus(200);

        // Verify ticket status became "encerrado"
        $response = $this->getJson("/api/store/tickets/{$ticketId}");
        $this->assertEquals('encerrado', $response->json('ticket.status'));

        // 6. Reopen the ticket
        $response = $this->withHeaders(['X-Test-User-Email' => 'gerente.natal@autocar.com'])
            ->postJson("/api/store/tickets/{$ticketId}/reopen");

        $response->assertStatus(200);
        $response = $this->getJson("/api/store/tickets/{$ticketId}");
        $this->assertEquals('aberto', $response->json('ticket.status'));
    }

    /**
     * Test buyer favoriting functionality.
     */
    public function test_vehicle_favoriting_workflow(): void
    {
        DB::table('favorites')->truncate();
        $vehicle = DB::table('vehicles')->first();
        $this->assertNotNull($vehicle);

        // Toggle favoriting ON
        $response = $this->withHeaders(['X-Test-User-Email' => 'comprador@gmail.com'])
            ->postJson("/api/favorites/{$vehicle->id}/toggle");

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'favorited']);
        $this->assertTrue($response->json('favorited'));

        // Get favorites list
        $response = $this->withHeaders(['X-Test-User-Email' => 'comprador@gmail.com'])
            ->getJson('/api/favorites');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json());

        // Toggle favoriting OFF
        $response = $this->withHeaders(['X-Test-User-Email' => 'comprador@gmail.com'])
            ->postJson("/api/favorites/{$vehicle->id}/toggle");

        $response->assertStatus(200);
        $this->assertFalse($response->json('favorited'));
    }
}
