<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipo_local' => $this->equipoLocal->nombre ?? null,
            'equipo_visitante' => $this->equipoVisitante->nombre ?? null,
            'fecha_hora' => $this->fecha_hora,
            'goles_local' => $this->goles_local,
            'goles_visitante' => $this->goles_visitante,
            'estado' => $this->estado,
            'etapa' => $this->etapa->nombre ?? null,
        ];
    }
}