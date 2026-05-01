<?php

namespace App\Events;

use App\Models\Juego;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JuegoFinalizado
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Juego $juego
    ) {
        //
    }
}
