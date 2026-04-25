<?php

namespace App\Enums;

enum Permiso: string
{
    case VER_DASHBOARD = 'ver dashboard';
    case VER_PARTIDOS = 'ver partidos';
    case CREAR_PREDICCIONES = 'crear predicciones';
    case ACTUALIZAR_RESULTADOS = 'actualizar resultados';
    case VER_REPORTES = 'ver reportes';
    case GESTIONAR_USUARIOS = 'gestionar usuarios';

    // Opcional: obtener todos los valores para el seeder
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
