<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Quiniela\Events\WinnersAnnounced;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WinnersNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public array $winners,
        public string $prizeDescription
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
            'winners' => $this->winners,
            'prize_description' => $this->prizeDescription,
            'message' => "Ganadores anunciados para {$this->quinielaName}",
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
            'winners' => $this->winners,
            'prize_description' => $this->prizeDescription,
            'message' => "Ganadores anunciados para {$this->quinielaName}",
        ];
    }
}
