<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<City>
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state_id' => State::inRandomOrder()->first() ?? State::factory(),
            'name' => $this->faker->city(),
            'ibge_code' => $this->faker->unique()->numerify('#######'),
            'latitude' => $this->faker->latitude(-33.0, 4.0),
            'longitude' => $this->faker->longitude(-73.0, -34.0),
        ];
    }
}
