<?php

namespace App\Http\Controllers;

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

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:usuarios'],
            'correo_electronico' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->authService->register($request->only(['nombre_completo', 'nombre_usuario', 'correo_electronico', 'password']));

        $token = $user->createToken('auth-token')->plainTextToken;

        // Log successful registration
        $this->auditLogger->logRegister($user->id);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'correo_electronico' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            $user = $this->authService->login($request->only(['correo_electronico', 'password']));

            $token = $user->createToken('auth-token')->plainTextToken;

            // Log successful login
            $this->auditLogger->logLogin($user->id);

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            // Log failed login attempt
            $this->auditLogger->logFailedLogin($request->input('correo_electronico'));

            // Re-throw the exception to maintain original behavior
            throw $e;
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
