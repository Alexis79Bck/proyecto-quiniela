<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Services\Toast\ToastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToastController extends Controller
{
    public function __construct(
        protected ToastService $toastService
    ) {}

    public function broadcast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', ToastType::values()),
            'message' => 'required|string|max:500',
            'title' => 'nullable|string|max:100',
            'duration' => 'nullable|integer|min:1000|max:30000',
            'dismissible' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $type = ToastType::from($validated['type']);

        $sent = $this->toastService->broadcast(
            $user->id,
            $type,
            $validated['message'],
            $validated['title'] ?? null,
            [
                'duration' => $validated['duration'] ?? null,
                'dismissible' => $validated['dismissible'] ?? null,
            ]
        );

        if (!$sent) {
            return response()->json([
                'message' => 'Error al enviar la notificación',
            ], 500);
        }

        return response()->json([
            'message' => 'Toast enviado correctamente',
            'data' => $this->toastService->formatToast(
                $type,
                $validated['message'],
                $validated['title'] ?? null
            ),
        ]);
    }

    public function broadcastToUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:' . implode(',', ToastType::values()),
            'message' => 'required|string|max:500',
            'title' => 'nullable|string|max:100',
            'duration' => 'nullable|integer|min:1000|max:30000',
            'dismissible' => 'nullable|boolean',
        ]);

        $type = ToastType::from($validated['type']);

        $sent = $this->toastService->broadcast(
            $validated['user_id'],
            $type,
            $validated['message'],
            $validated['title'] ?? null,
            [
                'duration' => $validated['duration'] ?? null,
                'dismissible' => $validated['dismissible'] ?? null,
            ]
        );

        if (!$sent) {
            return response()->json([
                'message' => 'Error al enviar la notificación',
            ], 500);
        }

        return response()->json([
            'message' => 'Toast enviado al usuario correctamente',
            'data' => $this->toastService->formatToast(
                $type,
                $validated['message'],
                $validated['title'] ?? null
            ),
        ]);
    }

    public function types(): JsonResponse
    {
        return response()->json([
            'types' => $this->toastService->getTypes(),
        ]);
    }
}