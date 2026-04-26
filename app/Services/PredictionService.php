<?php

namespace App\Services;

use App\Models\Prediccion;
use App\Models\Juego;
use App\Models\Usuario;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use App\Repositories\Contracts\PrediccionRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class PredictionService
{
    public function __construct(
        private PrediccionRepositoryInterface $prediccionRepository,
        private JuegoRepositoryInterface $juegoRepository
    ) {}

    public function createPrediction(Usuario $user, Juego $match, array $data): Prediccion
    {
        // Check if prediction is allowed (before deadline)
        if (!$this->isPredictionAllowed($match)) {
            throw new \Exception('Prediction deadline has passed.');
        }

        return $this->prediccionRepository->create([
            'usuario_id' => $user->id,
            'juego_id' => $match->id,
            'equipo_local_prediccion' => $data['goles_local'],
            'equipo_visitante_prediccion' => $data['goles_visitante'],
        ]);
    }

    public function updatePrediction(Prediccion $prediction, array $data): Prediccion
    {
        if (!$this->isPredictionAllowed($prediction->juego)) {
            throw new \Exception('Prediction deadline has passed.');
        }

        $this->prediccionRepository->update($prediction->id, [
            'equipo_local_prediccion' => $data['goles_local'],
            'equipo_visitante_prediccion' => $data['goles_visitante'],
        ]);

        return $prediction->fresh();
    }

    public function calculateScore(Prediccion $prediction): int
    {
        return $this->prediccionRepository->calculatePoints($prediction->id);
    }

    private function isPredictionAllowed(Juego $match): bool
    {
        $deadlineHours = Config::get('quiniela.deadlines.prediction_deadline_hours');
        $deadline = Carbon::parse($match->fecha_hora)->subHours($deadlineHours);
        return now()->lessThan($deadline);
    }
}