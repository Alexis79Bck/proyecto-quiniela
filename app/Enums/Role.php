<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'Administrador';
    case JUGADOR = 'Jugador';   // o 'Miembro', 'Participante'

    // Opcional: mapeo de qué permisos tiene cada rol
    public function permisos(): array
    {
        return match($this) {
            self::ADMIN => Permission::values(), // todos
            self::JUGADOR => [
                Permission::VER_PARTIDOS->value,
                Permission::CREAR_PREDICCIONES->value,
            ],
        };
    }
}
