<?php

namespace Database\Seeders;

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

        foreach ($datos as $index => $grupo) {
            $grupoId = $index + 1; // Los IDs de grupos comienzan en 1

            foreach ($grupo['selecciones'] as $seleccion) {
                $codigoIso = $seleccion['metadata']['iso_3166_1_alfa_2'];

                Equipo::create([
                    'nombre' => $seleccion['nombre'],
                    'codigo_fifa' => $seleccion['metadata']['codigo_fifa'],
                    'url_bandera' => asset("images/flags/svg/{$codigoIso}.png"),
                    'grupo_id' => $grupoId,
                ]);
            }
        }

        $this->command->info('✅ Equipos creados exitosamente.');
    }
}
