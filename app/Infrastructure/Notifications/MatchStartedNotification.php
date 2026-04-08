<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Match\Events\MatchStarted;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public string $startTime,
        public string $stage
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
            'match_id' => $this->matchId,
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
            'start_time' => $this->startTime,
            'stage' => $this->stage,
            'message' => "Partido iniciado: {$this->homeTeam} vs {$this->awayTeam}",
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
            'match_id' => $this->matchId,
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
            'start_time' => $this->startTime,
            'stage' => $this->stage,
            'message' => "Partido iniciado: {$this->homeTeam} vs {$this->awayTeam}",
        ];
    }
}
