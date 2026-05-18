<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Store;
use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreSetting>
 */
class StoreSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<StoreSetting>
     */
    protected $model = StoreSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keys = ['accent_color', 'address', 'phone', 'whatsapp_number', 'tagline', 'plan'];
        $key = $this->faker->randomElement($keys);

        $value = match ($key) {
            'accent_color' => $this->faker->randomElement(['#f59e0b', '#e11d48', '#2563eb', '#4f46e5']),
            'address' => $this->faker->address(),
            'phone', 'whatsapp_number' => $this->faker->phoneNumber(),
            'tagline' => $this->faker->sentence(),
            'plan' => $this->faker->randomElement(['mensal', 'semestral', 'anual']),
            default => $this->faker->word(),
        };

        return [
            'store_id' => Store::inRandomOrder()->first() ?? Store::factory(),
            'key' => $key,
            'value' => $value,
        ];
    }
}
