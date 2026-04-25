<?php

namespace Database\Seeders;

use App\Models\Etapa;
use Illuminate\Database\Seeder;

class EtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etapas = [
            ['nombre' => 'Fase de Grupos', 'descripcion' => 'La fase inicial del torneo donde los equipos se dividen en grupos.'],
            ['nombre' => '16avos de Final', 'descripcion' => 'La primera ronda de eliminación directa después de la fase de grupos.'],
            ['nombre' => 'Octavos de Final', 'descripcion' => 'La segunda ronda de eliminación directa después de la fase de grupos.'],
            ['nombre' => 'Cuartos de Final', 'descripcion' => 'La ronda de eliminación directa que sigue a los octavos de final.'],
            ['nombre' => 'Semifinales', 'descripcion' => 'La ronda que determina los finalistas del torneo.'],
            ['nombre' => 'Final', 'descripcion' => 'El partido decisivo para determinar al campeón del torneo.'],
        ];

        foreach ($etapas as $etapa) {
            Etapa::create($etapa);
        }

        $this->command->info('✅ Etapas creadas exitosamente.');
    }
}
