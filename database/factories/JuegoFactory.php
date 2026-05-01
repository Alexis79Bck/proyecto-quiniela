<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Juego>
 */
class JuegoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'etapa_id' => \App\Models\Etapa::factory(),
            'equipo_local_id' => \App\Models\Equipo::factory(),
            'equipo_visitante_id' => \App\Models\Equipo::factory(),
            'fecha_hora' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'estado' => 'Programado',
            'equipo_local_goles' => 0,
            'equipo_visitante_goles' => 0,
        ];
    }
}
