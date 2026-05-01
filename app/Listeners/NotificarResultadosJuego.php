<?php

namespace App\Listeners;

use App\Events\JuegoFinalizado;
use App\Notifications\ResultadoJuegoNotificacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification; // Assuming this notification exists

class NotificarResultadosJuego
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JuegoFinalizado $event): void
    {
        try {
            $juego = $event->juego;

            // Log the result notification
            Log::info("Resultado del juego {$juego->id}: {$juego->equipo_local_goles}-{$juego->equipo_visitante_goles} notificado");

            // Here you would typically send notifications to users
            // For example, using Laravel's notification system:
            // Notification::send($juego->predicciones->pluck('usuario'), new ResultadoJuegoNotificacion($juego));

            // For now, we'll just log it
        } catch (\Exception $e) {
            Log::error("Error al notificar resultados del juego {$event->juego->id}: ".$e->getMessage());
        }
    }
}
