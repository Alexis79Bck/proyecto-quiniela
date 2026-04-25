<?php

namespace App\Services;

use App\Models\Juego;
use App\Models\Etapa;
use Illuminate\Support\Collection;

class CalendarService
{
    public function getMatchesByStage(int $stageId): Collection
    {
        return Juego::where('etapa_id', $stageId)->with(['equipoLocal', 'equipoVisitante'])->get();
    }

    public function getUpcomingMatches(): Collection
    {
        return Juego::where('fecha_hora', '>', now())
            ->with(['equipoLocal', 'equipoVisitante'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function getMatchesByDate(string $date): Collection
    {
        return Juego::whereDate('fecha_hora', $date)
            ->with(['equipoLocal', 'equipoVisitante'])
            ->get();
    }

    public function getStages(): Collection
    {
        return Etapa::all();
    }
}