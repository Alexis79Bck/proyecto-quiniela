<?php

namespace App\Services\Notification;

use App\Models\User;
use App\DTO\NotificationDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Obtener notificaciones no leídas desde una marca de tiempo específica para un usuario.
     *
     * @param Carbon $since
     * @param User $user
     * @return Collection
     */
    public function getUnreadSince(Carbon $since, User $user): Collection
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
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function markAsRead(string $notificationId, User $user)
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
     * @param User $user
     * @return int Número de notificaciones marcadas como leídas
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Obtener las últimas notificaciones para un usuario.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getLatest(User $user, int $limit = 10): Collection
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