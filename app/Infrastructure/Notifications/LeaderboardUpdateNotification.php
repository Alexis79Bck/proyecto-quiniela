<?php

namespace App\Infrastructure\Notifications;

use App\Events\LeaderboardUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaderboardUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public array $topUsers,
        public int $totalParticipants
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'quiniela_id' => $this->quinielaId,
            'quiniela_name' => $this->quinielaName,
            'top_users' => $this->topUsers,
            'total_participants' => $this->totalParticipants,
            'message' => "Clasificación actualizada para {$this->quinielaName}",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quiniela_id' => $this->quinielaId,
            'quiniela_name' => $this->quinielaName,
            'top_users' => $this->topUsers,
            'total_participants' => $this->totalParticipants,
            'message' => "Clasificación actualizada para {$this->quinielaName}",
        ];
    }
}
