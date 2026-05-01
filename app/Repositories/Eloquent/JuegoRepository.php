<?php

namespace App\Repositories\Eloquent;

use App\Models\Juego;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class JuegoRepository extends BaseEloquentRepository implements JuegoRepositoryInterface
{
    public function __construct(Juego $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Juego
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Juego
    {
        return parent::find($id);
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }

    public function update(int $id, array $attributes): bool
    {
        $juego = $this->find($id);
        if ($juego) {
            return $juego->update($attributes);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $juego = $this->find($id);
        if ($juego) {
            return $juego->delete();
        }

        return false;
    }

    public function getByStage(int $stageId): Collection
    {
        return $this->model->where('etapa_id', $stageId)->with(['equipoLocal', 'equipoVisitante'])->get();
    }

    public function getUpcoming(): Collection
    {
        return $this->model->where('fecha_hora', '>', now())
            ->with(['equipoLocal', 'equipoVisitante'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function getByDate(string $date): Collection
    {
        return $this->model->whereDate('fecha_hora', $date)
            ->with(['equipoLocal', 'equipoVisitante'])
            ->get();
    }

    public function getWithPredictions(int $id): ?Juego
    {
        return $this->model->with(['predicciones.usuario'])->find($id);
    }
}