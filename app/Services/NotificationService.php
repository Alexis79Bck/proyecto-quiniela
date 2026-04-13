<?php

namespace App\Services;

use App\Domain\User\Models\User;
use App\DTO\NotificationDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get unread notifications since a specific timestamp for a user.
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
     * Mark a notification as read for a user.
     *
     * @param string $notificationId
     * @param User $user
     * @return bool
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param User $user
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Get latest notifications for a user.
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