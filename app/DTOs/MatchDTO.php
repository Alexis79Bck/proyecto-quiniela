<?php

namespace App\DTOs;

class MatchDTO
{
    public function __construct(
        public int $id,
        public string $equipo_local,
        public string $equipo_visitante,
        public string $fecha_hora,
        public ?int $goles_local = null,
        public ?int $goles_visitante = null,
        public string $estado,
        public ?int $etapa_id = null
    ) {}
}