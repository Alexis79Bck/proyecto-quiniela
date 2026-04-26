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
            'equipo_local_prediccion' => $this->equipo_local_prediccion,
            'equipo_visitante_prediccion' => $this->equipo_visitante_prediccion,
            'puntos' => $this->puntos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}