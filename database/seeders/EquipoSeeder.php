<?php

namespace Database\Seeders;

use App\Enums\Grupo;
use App\Models\Equipo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EquipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('grupos_equipos_fifa_wc2026.json');
        $datos = json_decode(File::get($jsonPath), true);

        foreach ($datos as $grupo) {
            if (!isset($grupo['grupo'], $grupo['selecciones'])) {
                $this->command->error('⚠️ Formato de datos incorrecto en el JSON. Se esperaba "grupo" y "selecciones".');
                continue;
            }

            if (!Grupo::isValid($grupo['grupo'])) {
                $this->command->error("⚠️ Grupo inválido: {$grupo['grupo']}. Se esperaba una letra de A a L.");
                continue;
            }

            $grupoLetra = $grupo['grupo']; // A, B, C, etc.

            foreach ($grupo['selecciones'] as $seleccion) {
                $codigoIso = $seleccion['metadata']['iso_3166_1_alfa_2'];

                Equipo::create([
                    'nombre' => $seleccion['nombre'],
                    'codigo_fifa' => $seleccion['metadata']['codigo_fifa'],
                    'url_bandera' => asset("images/flags/svg/{$codigoIso}.svg"),
                    'grupo' => $grupoLetra,
                ]);
            }
        }

        $this->command->info('✅ Equipos creados exitosamente.');
    }
}
