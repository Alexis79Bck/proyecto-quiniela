<?php

namespace App\Listeners;

use App\Events\JuegoFinalizado;
use App\Services\JuegoService;
use Illuminate\Support\Facades\Log;

class ProcesarPuntosPredicciones
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private JuegoService $juegoService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JuegoFinalizado $event): void
    {
        try {
            $juego = $event->juego;

            // Process the results to calculate points for predictions
            $this->juegoService->procesarResultados($juego);

            Log::info("Puntos calculados para las predicciones del juego {$juego->id}");
        } catch (\Exception $e) {
            Log::error("Error al procesar puntos para el juego {$event->juego->id}: ".$e->getMessage());
        }
    }
}
