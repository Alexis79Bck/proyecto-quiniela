<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PredictionResource extends JsonResource
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
            'usuario_id' => $this->usuario_id,
            'juego_id' => $this->juego_id,
            'goles_local' => $this->goles_local,
            'goles_visitante' => $this->goles_visitante,
            'puntos' => $this->puntos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}