<?php

namespace App\Services\Notification;

use App\Models\Usuario;
use App\DTO\NotificationDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Obtener notificaciones no leídas desde una marca de tiempo específica para un usuario.
     *
     * @param Carbon $since
     * @param Usuario $user
     * @return Collection
     */
    public function getUnreadSince(Carbon $since, Usuario $user): Collection
    {
        $notifications = $user->unreadNotifications()
            ->where('created_at', '>', $since)
            ->orderBy('created_at', 'desc')
            ->get();

        return $notifications->map(function ($notification) {
            return NotificationDTO::fromModel($notification);
        });
    }

    /**
     * Marcar una notificación como leída para un usuario.
     *
     * @param string $notificationId
     * @param Usuario $user
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function markAsRead(string $notificationId, Usuario $user)
    {
        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return null;
        }

        $notification->markAsRead();
        return $notification;
    }

    /**
     * Marcar todas las notificaciones como leídas para un usuario.
     *
     * @param Usuario $user
     * @return int Número de notificaciones marcadas como leídas
     */
    public function markAllAsRead(Usuario $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Obtener las últimas notificaciones para un usuario.
     *
     * @param Usuario $user
     * @param int $limit
     * @return Collection
     */
    public function getLatest(Usuario $user, int $limit = 10): Collection
    {
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $notifications->map(function ($notification) {
            return NotificationDTO::fromModel($notification);
        });
    }
}