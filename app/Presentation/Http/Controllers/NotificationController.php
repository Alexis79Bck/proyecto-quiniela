<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $notifications->items(),
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

        return response()->json([
            'data' => $notifications->items(),
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
        
        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notificación marcada como leída',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Todas las notificaciones marcadas como leídas',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $notificationId): JsonResponse
    {
        $user = Auth::user();
        
        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notificación no encontrada',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notificación eliminada',
        ]);
    }

    /**
     * Get notification count for the authenticated user.
     */
    public function count(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
        ]);
    }
}
