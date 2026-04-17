<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Quiniela\Events\NewQuinielaAvailable;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewQuinielaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public string $description,
        public string $startDate,
        public string $endDate
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [PusherChannel::class, 'database'];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'quiniela_id' => $this->quinielaId,
            'quiniela_name' => $this->quinielaName,
            'description' => $this->description,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'message' => "Nueva quiniela disponible: {$this->quinielaName}",
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
            'description' => $this->description,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'message' => "Nueva quiniela disponible: {$this->quinielaName}",
        ];
    }
}
