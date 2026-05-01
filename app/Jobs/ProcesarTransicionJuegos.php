<?php

namespace App\Jobs;

use App\Services\JuegoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarTransicionJuegos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private JuegoService $juegoService
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting automatic game state transitions processing');

            // Process PROGRAMADO games that should be started (fecha_hora <= now)
            $this->procesarProgramadosVencidos();

            Log::info('Automatic game state transitions processing completed');
        } catch (\Exception $e) {
            Log::error('Error in ProcesarTransicionJuegos job: '.$e->getMessage());
        }
    }

    /**
     * Process PROGRAMADO games that have passed their start time.
     */
    private function procesarProgramadosVencidos(): void
    {
        $programadosVencidos = $this->juegoService->juegoRepository->model
            ->where('estado', 'Programado')
            ->where('fecha_hora', '<=', now())
            ->get();

        foreach ($programadosVencidos as $juego) {
            try {
                $this->juegoService->iniciarJuego($juego->id);
                Log::info("Juego {$juego->id} iniciado automáticamente");
            } catch (\Exception $e) {
                Log::warning("Failed to start juego {$juego->id}: ".$e->getMessage());
            }
        }
    }

    /**
     * Optional: Process EN_PROGRESO games that should be auto-finalized after a timeout.
     * This is commented out as it requires defining what constitutes a "timeout" for a game.
     */
    /*
    private function procesarEnProgresoTimeout(): void
    {
        // Define timeout period (e.g., 4 hours after start)
        $timeoutHours = 4;

        $enProgresoTimeout = $this->juegoService->juegoRepository->model
            ->where('estado', 'En Progreso')
            ->where('fecha_hora', '<=', now()->subHours($timeoutHours))
            ->get();

        foreach ($enProgresoTimeout as $juego) {
            try {
                // This would require having actual scores, so we might need to
                // set default scores or handle this differently
                Log::info("Juego {$juego->id} marcado para finalización automática por timeout");
                // For now, just log it since we don't have actual scores to set
            } catch (\Exception $e) {
                Log::warning("Failed to auto-finalize juego {$juego->id}: " . $e->getMessage());
            }
        }
    }
    */
}
