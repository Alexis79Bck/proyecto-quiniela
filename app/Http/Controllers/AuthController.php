<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Infrastructure\Logging\AuditLogger\AuditLogger;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuditLogger $auditLogger
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $this->authService->register($validated);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Log successful registration
        $this->auditLogger->logRegister($user->id);

        $data = null;
        if ($user->hasRole('Administrador')) {
            $data = [
                'id' => $user->id,
                'fullname' => $user->nombre_completo,
                'username' => $user->nombre_usuario,
                'email' => $user->correo_electronico,
                'role' => 'Administrador',
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado con éxito',
            'token' => $token,
            'data' => $data,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->authService->login($validated);

            $token = $user->createToken('auth-token')->plainTextToken;

            // Log successful login
            $this->auditLogger->logLogin($user->id);

            $data = null;
            if ($user->hasRole('Administrador')) {
                $data = [
                    'id' => $user->id,
                    'fullname' => $user->nombre_completo,
                    'username' => $user->nombre_usuario,
                    'role' => 'Administrador',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Sesión iniciada correctamente',
                'token' => $token,
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            // Log failed login attempt
            $this->auditLogger->logFailedLogin($request->input('correo_electronico'));

            // Re-throw to let FormRequest handle? Actually FormRequest already passed validation.
            // This catch is for the authentication failure (invalid credentials).
            // We'll return a unified error response.
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas son incorrectas.',
                'data' => null,
            ], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        // Log logout
        $this->auditLogger->logLogout($request->user()->id);

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(Auth::user());
    }
}
