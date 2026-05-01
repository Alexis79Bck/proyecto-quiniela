<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function createUsuario(array $data): Usuario
    {
        return $this->usuarioRepository->create([
            'nombre_completo' => $data['nombre_completo'],
            'nombre_usuario' => $data['nombre_usuario'],
            'correo_electronico' => $data['correo_electronico'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateUsuario(Usuario $user, array $data): Usuario
    {
        $this->usuarioRepository->update($user->id, $data);

        return $user->fresh();
    }

    public function deleteUsuario(Usuario $user): bool
    {
        return $this->usuarioRepository->delete($user->id);
    }

    public function getUsuarioById(int $id): ?Usuario
    {
        return $this->usuarioRepository->find($id);
    }

    public function getUsuarioByCorreo(string $correo): ?Usuario
    {
        return $this->usuarioRepository->findByEmail($correo);
    }

    public function verificarPassword(string $password, string $hashedPassword): bool
    {
        return $this->usuarioRepository->checkUserPassword($password, $hashedPassword);
    }
}
