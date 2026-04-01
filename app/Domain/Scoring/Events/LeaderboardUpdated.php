<?php

namespace App\Domain\Scoring\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaderboardUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public array $topUsers,
        public int $totalParticipants
    ) {
        if ($quinielaId <= 0) {
            throw new \InvalidArgumentException('quinielaId must be positive');
        }
    }
}
