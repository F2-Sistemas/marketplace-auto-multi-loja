<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VehiclePhoneViewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_can_log_phone_view_for_vehicle(): void
    {
        $vehicle = DB::table('vehicles')->first();
        $this->assertNotNull($vehicle);

        $response = $this->postJson("/api/vehicles/{$vehicle->id}/phone-view", [
            'vehicle_id' => $vehicle->id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'phone_view_id',
        ]);

        $this->assertDatabaseHas('vehicle_phone_views', [
            'vehicle_id' => $vehicle->id,
            'vehicle_slug' => $vehicle->slug,
        ]);
    }
}
