<?php

namespace App\DTOs;

class MatchDTO
{
    public function __construct(
        public int $id,
        public string $equipo_local,
        public string $equipo_visitante,
        public string $fecha_hora,
        public ?int $equipo_local_goles = null,
        public ?int $equipo_visitante_goles = null,
        public string $estado,
        public ?int $etapa_id = null
    ) {}
}