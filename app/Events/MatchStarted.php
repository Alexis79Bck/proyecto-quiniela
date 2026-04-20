<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchStarted
{
    use Dispatchable, SerializesModels;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public string $startTime,
        public string $stage
    ) {
        if ($matchId <= 0) {
            throw new \InvalidArgumentException('matchId must be positive');
        }
    }
}
