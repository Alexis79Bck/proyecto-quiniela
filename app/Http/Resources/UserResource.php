<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'nombre_completo' => $this->nombre_completo,
            'nombre_usuario' => $this->nombre_usuario,
            'correo_electronico' => $this->correo_electronico,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
