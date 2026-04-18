<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Notification\NotificationService;
use App\DTO\NotificationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}
    /**
     * Get all notifications for the authenticated user.
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
     * Get unread notifications for the authenticated user.
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
     * Mark a notification as read.
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
     * Mark all notifications as read.
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