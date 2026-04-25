<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): Usuario
    {
        return Usuario::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateUser(Usuario $user, array $data): Usuario
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(Usuario $user): bool
    {
        return $user->delete();
    }

    public function getUserById(int $id): ?Usuario
    {
        return Usuario::find($id);
    }
}