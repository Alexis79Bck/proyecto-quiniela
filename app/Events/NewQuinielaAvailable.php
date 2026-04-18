<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewQuinielaAvailable
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $quinielaId,
        public string $quinielaName,
        public string $description,
        public string $startDate,
        public string $endDate
    ) {
        if ($quinielaId <= 0) {
            throw new \InvalidArgumentException('quinielaId must be positive');
        }
    }
}
