<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function createUser(array $data): Usuario
    {
        return $this->usuarioRepository->create([
            'nombre_completo' => $data['name'],
            'correo_electronico' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateUser(Usuario $user, array $data): Usuario
    {
        $this->usuarioRepository->update($user->id, $data);
        return $user->fresh();
    }

    public function deleteUser(Usuario $user): bool
    {
        return $this->usuarioRepository->delete($user->id);
    }

    public function getUserById(int $id): ?Usuario
    {
        return $this->usuarioRepository->find($id);
    }
}