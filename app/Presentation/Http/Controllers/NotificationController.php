<?php

namespace App\Presentation\Http\Controllers;

use App\DTO\NotificationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            'data' => NotificationDTO::fromModel($notification)->toArray(),
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

    /**
     * Get notifications since a specific timestamp for polling.
     */
    public function poll(Request $request): JsonResponse
    {
        $user = Auth::user();
        $since = $request->query('since', now()->subMinutes(5)->toISOString());
        $sinceCarbon = Carbon::parse($since);

        $notifications = $user->notifications()
            ->where('created_at', '>', $sinceCarbon)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Convert to DTOs
        $notificationDTOs = $notifications->map(function ($notification) {
            return NotificationDTO::fromModel($notification)->toArray();
        });

        return response()->json([
            'data' => $notificationDTOs,
            'meta' => [
                'last_check' => now()->toISOString(),
                'has_new' => $notificationDTOs->isNotEmpty(),
                'count' => $notificationDTOs->count(),
            ],
        ]);
    }

    /**
     * Get latest notifications for the authenticated user.
     */
    public function latest(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->query('limit', 10);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Convert to DTOs
        $notificationDTOs = $notifications->map(function ($notification) {
            return NotificationDTO::fromModel($notification)->toArray();
        });

        return response()->json([
            'data' => $notificationDTOs,
            'meta' => [
                'count' => $notificationDTOs->count(),
            ],
        ]);
    }

    /**
     * Health check endpoint for notification service.
     */
    public function health(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return response()->json([
            'status' => 'ok',
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
