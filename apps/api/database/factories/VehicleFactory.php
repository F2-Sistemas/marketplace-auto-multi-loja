<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\City;
use App\Models\Store;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Vehicle>
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brand = Brand::inRandomOrder()->first() ?? Brand::factory()->create();
        $vehicleModel = VehicleModel::where('brand_id', $brand->id)->inRandomOrder()->first() ?? VehicleModel::factory()->create(['brand_id' => $brand->id]);
        $city = City::inRandomOrder()->first() ?? City::factory()->create();
        
        $title = "{$brand->name} {$vehicleModel->name} " . $this->faker->randomElement(['Flex automatico', 'Diesel 4x4', 'TSI automatico']);
        $slug = Str::slug($title) . '-' . $this->faker->numberBetween(1000, 9999);

        return [
            'store_id' => Store::inRandomOrder()->first() ?? Store::factory(),
            'brand_id' => $brand->id,
            'vehicle_model_id' => $vehicleModel->id,
            'city_id' => $city->id,
            'title' => $title,
            'slug' => $slug,
            'version' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 45000, 250000),
            'year_manufacture' => $this->faker->numberBetween(2018, 2025),
            'year_model' => $this->faker->numberBetween(2019, 2026),
            'mileage' => $this->faker->numberBetween(5000, 120000),
            'color' => $this->faker->safeColorName(),
            'transmission' => $this->faker->randomElement(['automatico', 'manual']),
            'fuel' => $this->faker->randomElement(['flex', 'gasolina', 'diesel', 'eletrico']),
            'status' => 'available',
            'views_count' => $this->faker->numberBetween(0, 500),
        ];
    }
}
