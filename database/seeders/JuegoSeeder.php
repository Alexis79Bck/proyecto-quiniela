<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Juego;
use App\Models\Equipo;
use App\Enums\MatchStatus;
use Illuminate\Support\Str;

class JuegoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cargar calendario desde JSON
        $jsonPath = database_path('calendario_fifa_wc2026.json');
        $calendario = json_decode(File::get($jsonPath), true, flags:JSON_OBJECT_AS_ARRAY);

        foreach ($calendario as $partido) {
            // Parsear fecha y hora
            $fechaHora = \Carbon\Carbon::createFromFormat('d-m-Y H:i', "{$partido['fecha']} {$partido['hora']}");

            // Buscar equipos
            $equipoLocal =  Equipo::where('nombre', $partido['equipo_local'])->first();
            $equipoVisitante = Equipo::where('nombre', $partido['equipo_visitante'])->first();

            // Saltar si no se encuentran los equipos
            if (!$equipoLocal || !$equipoVisitante ) {
                $this->command->warn("⚠️  Equipos o etapa no encontrados para: {$partido['equipo_local']} vs {$partido['equipo_visitante']}");
                continue;
            }

            // Crear el juego
            Juego::create([
                'uuid' => Str::uuid(),
                'etapa_id' => 1,
                'equipo_local_id' => $equipoLocal->id,
                'equipo_visitante_id' => $equipoVisitante->id,
                'fecha_hora' => $fechaHora,
                'equipo_local_goles' => 0,
                'equipo_visitante_goles' => 0,
                'estado' => MatchStatus::PROGRAMADO,
            ]);
        }

        $this->command->info('✅ Juegos creados exitosamente.');
    }
}
