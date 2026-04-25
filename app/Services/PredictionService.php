<?php

namespace App\Services;

use App\Models\Prediccion;
use App\Models\Juego;
use App\Models\Usuario;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class PredictionService
{
    public function createPrediction(Usuario $user, Juego $match, array $data): Prediccion
    {
        // Check if prediction is allowed (before deadline)
        if (!$this->isPredictionAllowed($match)) {
            throw new \Exception('Prediction deadline has passed.');
        }

        return Prediccion::create([
            'usuario_id' => $user->id,
            'juego_id' => $match->id,
            'goles_local' => $data['goles_local'],
            'goles_visitante' => $data['goles_visitante'],
        ]);
    }

    public function updatePrediction(Prediccion $prediction, array $data): Prediccion
    {
        if (!$this->isPredictionAllowed($prediction->juego)) {
            throw new \Exception('Prediction deadline has passed.');
        }

        $prediction->update($data);
        return $prediction;
    }

    public function calculateScore(Prediccion $prediction): int
    {
        $match = $prediction->juego;

        if ($match->estado !== 'finalizado') {
            return 0;
        }

        $actualLocal = $match->goles_local;
        $actualVisitante = $match->goles_visitante;
        $predictedLocal = $prediction->goles_local;
        $predictedVisitante = $prediction->goles_visitante;

        if ($predictedLocal == $actualLocal && $predictedVisitante == $actualVisitante) {
            return Config::get('quiniela.scoring.exact_score');
        }

        $actualResult = $this->getMatchResult($actualLocal, $actualVisitante);
        $predictedResult = $this->getMatchResult($predictedLocal, $predictedVisitante);

        if ($actualResult === $predictedResult) {
            return Config::get('quiniela.scoring.correct_result');
        }

        return Config::get('quiniela.scoring.no_points');
    }

    private function isPredictionAllowed(Juego $match): bool
    {
        $deadlineHours = Config::get('quiniela.deadlines.prediction_deadline_hours');
        $deadline = Carbon::parse($match->fecha_hora)->subHours($deadlineHours);
        return now()->lessThan($deadline);
    }

    private function getMatchResult(int $local, int $visitante): string
    {
        if ($local > $visitante) return 'local';
        if ($local < $visitante) return 'visitante';
        return 'draw';
    }
}