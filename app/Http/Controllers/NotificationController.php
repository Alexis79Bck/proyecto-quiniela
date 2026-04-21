<?php

namespace App\Http\Controllers;

use App\Services\Notification\NotificationService;
use App\DTO\NotificationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}
    /**
     * Obtener todas las notificaciones para el usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Convert to DTOs
        $notificationDTOs = collect($notifications->items())->map(function ($notification) {
            return NotificationDTO::fromModel($notification)->toArray();
        });

        return response()->json([
            'data' => $notificationDTOs,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Obtener notificaciones no leídas para el usuario autenticado.
     */
    public function unread(Request $request): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Convert to DTOs
        $notificationDTOs = collect($notifications->items())->map(function ($notification) {
            return NotificationDTO::fromModel($notification)->toArray();
        });

        return response()->json([
            'data' => $notificationDTOs,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Marcar una notificación como leída.
     */
    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $user = Auth::user();

        $notification = $this->notificationService->markAsRead($notificationId, $user);

        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada',
            ], 404);
        }

        return response()->json([
            'message' => 'Notificación marcada como leída',
            'data' => NotificationDTO::fromModel($notification)->toArray(),
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'message' => 'Todas las notificaciones han sido marcadas como leídas',
            'count' => $count,
        ]);
    }
}