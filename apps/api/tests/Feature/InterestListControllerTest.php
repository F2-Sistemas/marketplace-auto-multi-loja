<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InterestListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_can_create_list_add_vehicle_and_publish_it(): void
    {
        $vehicle = DB::table('vehicles')->first();
        $this->assertNotNull($vehicle);

        $createResponse = $this->withHeaders([
            'X-Test-User-Email' => 'comprador@gmail.com',
        ])->postJson('/api/interest-lists', [
            'name' => 'SUVs premium',
            'description' => 'Carros para acompanhar com calma',
            'is_public' => false,
        ]);

        $createResponse->assertStatus(201);
        $createResponse->assertJsonStructure([
            'message',
            'list_id',
        ]);

        $listId = (int) $createResponse->json('list_id');

        $addResponse = $this->withHeaders([
            'X-Test-User-Email' => 'comprador@gmail.com',
        ])->postJson("/api/interest-lists/{$listId}/vehicles", [
            'vehicle_id' => $vehicle->id,
        ]);

        $addResponse->assertStatus(200);
        $addResponse->assertJson([
            'added' => true,
        ]);

        $duplicateResponse = $this->withHeaders([
            'X-Test-User-Email' => 'comprador@gmail.com',
        ])->postJson("/api/interest-lists/{$listId}/vehicles", [
            'vehicle_id' => $vehicle->id,
        ]);

        $duplicateResponse->assertStatus(200);
        $duplicateResponse->assertJson([
            'added' => false,
        ]);

        $list = DB::table('interest_lists')->where('id', $listId)->first();
        $this->assertNotNull($list);

        $updateResponse = $this->withHeaders([
            'X-Test-User-Email' => 'comprador@gmail.com',
        ])->putJson("/api/interest-lists/{$listId}", [
            'name' => 'SUVs premium',
            'description' => 'Carros para acompanhar com calma',
            'is_public' => true,
        ]);

        $updateResponse->assertStatus(200);

        $publicResponse = $this->getJson("/api/interest-lists/{$list->slug}");
        $publicResponse->assertStatus(200);
        $publicResponse->assertJsonPath('list.name', 'SUVs premium');
        $publicResponse->assertJsonPath('list.is_public', true);
        $publicResponse->assertJsonCount(1, 'vehicles');
    }

    public function test_private_list_returns_private_message_when_opened_publicly(): void
    {
        $vehicle = DB::table('vehicles')->first();
        $this->assertNotNull($vehicle);

        $createResponse = $this->withHeaders([
            'X-Test-User-Email' => 'comprador@gmail.com',
        ])->postJson('/api/interest-lists', [
            'name' => 'Privada',
            'description' => null,
            'is_public' => false,
        ]);

        $createResponse->assertStatus(201);
        $listId = (int) $createResponse->json('list_id');

        $list = DB::table('interest_lists')->where('id', $listId)->first();
        $this->assertNotNull($list);

        $response = $this->getJson("/api/interest-lists/{$list->slug}");
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'lista inexistente ou privada',
        ]);
    }
}
