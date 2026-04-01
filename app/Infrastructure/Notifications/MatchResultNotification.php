<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Match\Events\MatchResultAvailable;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MatchResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public int $homeScore,
        public int $awayScore,
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
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
            'stage' => $this->stage,
            'message' => "Resultado: {$this->homeTeam} {$this->homeScore} - {$this->awayScore} {$this->awayTeam}",
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
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
            'stage' => $this->stage,
            'message' => "Resultado: {$this->homeTeam} {$this->homeScore} - {$this->awayScore} {$this->awayTeam}",
        ];
    }
}
