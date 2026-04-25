<?php

namespace App\Services\Auth;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): Usuario
    {
        return Usuario::create([
            'nombre_completo' => $data['nombre_completo'],
            'nombre_usuario' => $data['nombre_usuario'],
            'correo_electronico' => $data['correo_electronico'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Attempt to authenticate a user
     */
    public function login(array $credentials): Usuario
    {
        $user = Usuario::where('correo_electronico', $credentials['correo_electronico'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'correo_electronico' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        return $user;
    }

    /**
     * Logout user by revoking tokens
     */
    public function logout(Usuario $user): void
    {
        $user->tokens()->delete();
    }
}
