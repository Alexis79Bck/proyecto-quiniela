<?php

namespace App\Enums;

enum Rol: string
{
    case ADMIN = 'Administrador';
    case JUGADOR = 'Jugador';   // o 'Miembro', 'Participante'

    // Opcional: mapeo de qué permisos tiene cada rol
    public function permisos(): array
    {
        return match($this) {
            self::ADMIN => Permiso::values(), // todos
            self::JUGADOR => [
                Permiso::VER_PARTIDOS->value,
                Permiso::CREAR_PREDICCIONES->value,
            ],
        };
    }
}
