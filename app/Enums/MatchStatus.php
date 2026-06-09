<?php

namespace App\Enums;

enum MatchStatus: string
{
    case PROGRAMADO = 'Programado';
    case EN_PROGRESO = 'En Progreso';
    case FINALIZADO = 'Finalizado';
    case SUSPENDIDO = 'Suspendido';
    case APLAZADO = 'Aplazado';
    case CANCELADO = 'Cancelado';
    case RETRASADO = 'Retrasado';
    case ANULADO = 'Anulado';

    public function getColor(): string
    {
        return match ($this) {
            self::PROGRAMADO => '#3b82f6', // Azul
            self::EN_PROGRESO => '#f59e0b', // Naranja
            self::FINALIZADO => '#10b981', // Verde
            self::SUSPENDIDO => '#ef4444', // Rojo
            self::APLAZADO => '#8b5cf6', // Morado
            self::CANCELADO => '#6b7280', // Gris
            self::RETRASADO => '#984102', // Marrón
            self::ANULADO => '#100b0b', // Negro
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
