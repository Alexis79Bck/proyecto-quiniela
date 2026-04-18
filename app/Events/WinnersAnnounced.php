<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WinnersAnnounced
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public array $winners,
        public string $prizeDescription
    ) {
        if ($quinielaId <= 0) {
            throw new \InvalidArgumentException('quinielaId must be positive');
        }
    }
}
