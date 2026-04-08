<?php

namespace App\Domain\Match\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchStarted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
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
