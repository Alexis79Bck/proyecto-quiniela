<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchResultAvailable
{
    use Dispatchable, SerializesModels;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(
        public int $matchId,
        public string $homeTeam,
        public string $awayTeam,
        public int $homeScore,
        public int $awayScore,
        public string $stage
    ) {
        if ($homeScore < 0) {
            throw new \InvalidArgumentException('homeScore must be non-negative');
        }
        if ($awayScore < 0) {
            throw new \InvalidArgumentException('awayScore must be non-negative');
        }
    }
}
