<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Juego;
use App\Models\Equipo;
use App\Models\Etapa;
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
        $calendario = json_decode(File::get($jsonPath), true);

        // Obtener todas las etapas disponibles
        $etapas = Etapa::all()->keyBy('nombre');

        // Obtener todos los equipos por nombre
        $equipos = Equipo::all()->keyBy('nombre');

        // Determinar la etapa para cada juego (basado en la fecha)
        $fechaFaseGrupos = \Carbon\Carbon::createFromFormat('d-m-Y', '27-06-2026');

        foreach ($calendario as $partido) {
            // Parsear fecha y hora
            $fechaHora = \Carbon\Carbon::createFromFormat('d-m-Y H:i', "{$partido['fecha']} {$partido['hora']}");

            // Determinar etapa (Fase de Grupos u otra)
            $etapaNombre = $fechaHora <= $fechaFaseGrupos ? 'Fase de Grupos' : 'Fase Final';
            $etapa = $etapas->get($etapaNombre);

            // Buscar equipos
            $equipoLocal = $equipos->get($partido['equipo_local']);
            $equipoVisitante = $equipos->get($partido['equipo_visitante']);

            // Saltar si no se encuentran los equipos
            if (!$equipoLocal || !$equipoVisitante || !$etapa) {
                $this->command->warn("⚠️  Equipos o etapa no encontrados para: {$partido['equipo_local']} vs {$partido['equipo_visitante']}");
                continue;
            }

            // Crear el juego
            Juego::create([
                'uuid' => Str::uuid(),
                'etapa_id' => $etapa->id,
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
