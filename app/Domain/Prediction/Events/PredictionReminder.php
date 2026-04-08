<?php

namespace App\Domain\Prediction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PredictionReminder
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $userId,
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public string $matchTime,
        public string $quinielaName
    ) {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('userId must be positive');
        }
        if ($matchId <= 0) {
            throw new \InvalidArgumentException('matchId must be positive');
        }
    }
}
