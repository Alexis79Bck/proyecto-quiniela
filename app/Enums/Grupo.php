<?php

namespace App\Enums;

enum Grupo: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';
    case I = 'I';
    case J = 'J';
    case K = 'K';
    case L = 'L';

    /**
     * Obtener todos los grupos como array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Verificar si un valor es un grupo válido
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Obtener todos los casos del enum
     */
    public static function obtenerCasos(): array
    {
        return self::cases();
    }
}
