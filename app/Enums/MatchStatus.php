<?php

namespace App\Enums;

enum MatchStatus: string
{
    case PROGRAMADO = 'Programado';
    case EN_PROGRESO = 'En Progreso';
    case FINALIZADO = 'Finalizado';

    public function getColor(): string
    {
        return match ($this) {
            self::PROGRAMADO => '#3b82f6', // Azul
            self::EN_PROGRESO => '#f59e0b', // Naranja
            self::FINALIZADO => '#10b981', // Verde
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

