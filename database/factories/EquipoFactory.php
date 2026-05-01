<?php

namespace Database\Factories;

use App\Models\Equipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipo>
 */
class EquipoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'codigo_fifa' => fake()->bothify('???'),
            'url_bandera' => fake()->imageUrl(),
            'grupo' => fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']),
        ];
    }
}
