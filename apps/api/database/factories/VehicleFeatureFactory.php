<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleFeature>
 */
class VehicleFeatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VehicleFeature>
     */
    protected $model = VehicleFeature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $features = [
            'Ar Condicionado', 'Direção Hidráulica', 'Vidros Elétricos', 'Travas Elétricas',
            'Alarme', 'Freio ABS', 'Airbag', 'Rodas de Liga Leve', 'Bancos de Couro',
            'Central Multimídia', 'Teto Solar', 'Câmera de Ré', 'Sensor de Estacionamento',
        ];

        return [
            'vehicle_id' => Vehicle::inRandomOrder()->first() ?? Vehicle::factory(),
            'name' => $this->faker->randomElement($features),
        ];
    }
}
