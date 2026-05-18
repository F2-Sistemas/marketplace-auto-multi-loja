<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<State>
     */
    protected $model = State::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = [
            ['name' => 'São Paulo', 'uf' => 'SP'],
            ['name' => 'Rio Grande do Norte', 'uf' => 'RN'],
            ['name' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['name' => 'Minas Gerais', 'uf' => 'MG'],
            ['name' => 'Paraná', 'uf' => 'PR'],
            ['name' => 'Santa Catarina', 'uf' => 'SC'],
            ['name' => 'Rio Grande do Sul', 'uf' => 'RS'],
        ];

        $state = $this->faker->unique()->randomElement($states);

        return [
            'name' => $state['name'],
            'uf' => $state['uf'],
        ];
    }
}
