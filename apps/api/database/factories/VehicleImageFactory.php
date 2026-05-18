<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleImage>
 */
class VehicleImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VehicleImage>
     */
    protected $model = VehicleImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = [
            'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1555215695-3004980ad54e?q=80&w=800&auto=format&fit=crop',
        ];

        return [
            'vehicle_id' => Vehicle::inRandomOrder()->first() ?? Vehicle::factory(),
            'path' => $this->faker->randomElement($images),
            'is_featured' => $this->faker->boolean(25), // 25% chance of being featured
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
