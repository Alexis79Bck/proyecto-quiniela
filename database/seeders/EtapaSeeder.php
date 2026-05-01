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
            ['nombre' => 'Fase de Grupos'],
            ['nombre' => '16avos de Final'],
            ['nombre' => 'Octavos de Final'],
            ['nombre' => 'Cuartos de Final'],
            ['nombre' => 'Semifinales'],
            ['nombre' => 'Final'],
        ];

        foreach ($etapas as $etapa) {
            Etapa::create($etapa);
        }

        $this->command->info('✅ Etapas creadas exitosamente.');
    }
}
