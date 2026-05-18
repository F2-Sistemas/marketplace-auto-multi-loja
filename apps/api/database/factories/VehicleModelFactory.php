<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\VehicleModel>
     */
    protected $model = VehicleModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modelNames = ['Corolla', 'Polo', 'Civic', 'Compass', '320i', 'Onix', 'Mustang', 'Creta'];
        $name = $this->faker->randomElement($modelNames);

        return [
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => strtolower($name),
        ];
    }
}
