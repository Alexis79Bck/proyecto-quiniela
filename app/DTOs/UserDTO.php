<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public int $id,
        public string $nombre_completo,
        public string $nombre_usuarioo,
        public string $correo_electronico,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}

    public static function fromModel($usuario): self
    {
        return new self(
            id: $usuario->id,
            nombre_completo: $usuario->nombre_completo,
            nombre_usuarioo: $usuario->nombre_usuario,
            correo_electronico: $usuario->correo_electronico,
            created_at: $usuario->created_at?->toDateTimeString(),
            updated_at: $usuario->updated_at?->toDateTimeString()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre_completo' => $this->nombre_completo,
            'nombre_usuarioo' => $this->nombre_usuarioo,
            'correo_electronico' => $this->correo_electronico,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}