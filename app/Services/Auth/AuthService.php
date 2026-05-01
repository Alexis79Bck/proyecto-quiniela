<?php

namespace App\Services\Auth;

use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    /**
     * Register a new user
     */
    public function register(array $data): Usuario
    {
        return $this->usuarioService->createUsuario($data);
    }

    /**
     * Attempt to authenticate a user
     */
    public function login(array $credentials): Usuario
    {
        $user = $this->usuarioService->getUsuarioByCorreo($credentials['correo_electronico']);

        if (! $user || ! $this->usuarioService->verificarPassword($credentials['password'], $user->password)) {
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
