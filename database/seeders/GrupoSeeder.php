<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = [
            ['nombre' => 'Grupo A'],
            ['nombre' => 'Grupo B'],
            ['nombre' => 'Grupo C'],
            ['nombre' => 'Grupo D'],
            ['nombre' => 'Grupo E'],
            ['nombre' => 'Grupo F'],
            ['nombre' => 'Grupo G'],
            ['nombre' => 'Grupo H'],
            ['nombre' => 'Grupo I'],
            ['nombre' => 'Grupo J'],
            ['nombre' => 'Grupo K'],
            ['nombre' => 'Grupo L'],
        ];

        foreach ($grupos as $grupo) {
            \App\Models\Grupo::create($grupo);
        }

        $this->command->info('✅ Grupos creados exitosamente.');
    }
}
