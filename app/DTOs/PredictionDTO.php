<?php

namespace App\DTOs;

class PredictionDTO
{
    public function __construct(
        public int $id,
        public int $usuario_id,
        public int $juego_id,
        public int $equipo_local_prediccion,
        public int $equipo_visitante_prediccion,
        public ?int $puntos = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}
}