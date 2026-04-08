<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Prediction\Events\PredictionReminder;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PredictionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $userId,
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public string $matchTime,
        public string $quinielaName
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
            'user_id' => $this->userId,
            'match_id' => $this->matchId,
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
            'match_time' => $this->matchTime,
            'quiniela_name' => $this->quinielaName,
            'message' => "Recordatorio: Realiza tu predicción para {$this->homeTeam} vs {$this->awayTeam}",
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
            'user_id' => $this->userId,
            'match_id' => $this->matchId,
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
            'match_time' => $this->matchTime,
            'quiniela_name' => $this->quinielaName,
            'message' => "Recordatorio: Realiza tu predicción para {$this->homeTeam} vs {$this->awayTeam}",
        ];
    }
}
