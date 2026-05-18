<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Store;
use App\Models\StoreDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreDomain>
 */
class StoreDomainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<StoreDomain>
     */
    protected $model = StoreDomain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $domain = $this->faker->unique()->domainName();

        return [
            'store_id' => Store::inRandomOrder()->first() ?? Store::factory(),
            'domain' => $domain,
            'type' => $this->faker->randomElement(['internal', 'custom']),
            'is_primary' => $this->faker->boolean(),
            'is_verified' => $this->faker->boolean(),
            'verified_at' => now(),
            'status' => 'active',
        ];
    }
}
