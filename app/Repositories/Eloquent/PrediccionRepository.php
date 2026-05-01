<?php

namespace App\Repositories\Eloquent;

use App\Models\Prediccion;
use App\Repositories\Contracts\PrediccionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PrediccionRepository extends BaseEloquentRepository implements PrediccionRepositoryInterface
{
    public function __construct(Prediccion $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Prediccion
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Prediccion
    {
        return parent::find($id);
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }

    public function update(int $id, array $attributes): bool
    {
        $prediccion = $this->find($id);
        if ($prediccion) {
            return $prediccion->update($attributes);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $prediccion = $this->find($id);
        if ($prediccion) {
            return $prediccion->delete();
        }

        return false;
    }

    public function getByUser(int $userId): Collection
    {
        return $this->model->where('usuario_id', $userId)->with(['juego'])->get();
    }

    public function getByMatch(int $matchId): Collection
    {
        return $this->model->where('juego_id', $matchId)->with(['usuario'])->get();
    }

    public function getByUserAndMatch(int $userId, int $matchId): ?Prediccion
    {
        return $this->model->where('usuario_id', $userId)
            ->where('juego_id', $matchId)
            ->first();
    }

    public function calculatePoints(int $predictionId): int
    {
        $prediction = $this->find($predictionId);
        if (!$prediction || !$prediction->juego) {
            return 0;
        }

        $match = $prediction->juego;
        if ($match->estado !== 'finalizado') {
            return 0;
        }

        // Assuming the fields are equipo_local_prediccion and equipo_visitante_prediccion
        $predictedLocal = $prediction->equipo_local_prediccion;
        $predictedVisitante = $prediction->equipo_visitante_prediccion;
        $actualLocal = $match->equipo_local_goles;
        $actualVisitante = $match->equipo_visitante_goles;

        if ($predictedLocal == $actualLocal && $predictedVisitante == $actualVisitante) {
            return config('quiniela.scoring.exact_score', 3);
        }

        $actualResult = $this->getMatchResult($actualLocal, $actualVisitante);
        $predictedResult = $this->getMatchResult($predictedLocal, $predictedVisitante);

        if ($actualResult === $predictedResult) {
            return config('quiniela.scoring.correct_result', 1);
        }

        return config('quiniela.scoring.no_points', 0);
    }

    private function getMatchResult(int $local, int $visitante): string
    {
        if ($local > $visitante) return 'local';
        if ($local < $visitante) return 'visitante';
        return 'draw';
    }
}