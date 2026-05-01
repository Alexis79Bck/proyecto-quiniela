<?php

namespace App\Repositories\Eloquent;

use App\Models\Juego;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

    // New methods implementation
    public function getProgramados(): Collection
    {
        return $this->model->where('estado', 'Programado')
            ->with(['equipoLocal', 'equipoVisitante', 'etapa'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function getEnProgreso(): Collection
    {
        return $this->model->where('estado', 'En Progreso')
            ->with(['equipoLocal', 'equipoVisitante', 'etapa'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function getFinalizados(): Collection
    {
        return $this->model->where('estado', 'Finalizado')
            ->with(['equipoLocal', 'equipoVisitante', 'etapa'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('estado', $status)
            ->with(['equipoLocal', 'equipoVisitante', 'etapa'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function finalizarJuego(int $id, array $resultados): bool
    {
        return DB::transaction(function () use ($id, $resultados) {
            $juego = $this->find($id);

            if (! $juego) {
                return false;
            }

            // Validate resultados
            $golesLocal = $resultados['goles_local'] ?? null;
            $golesVisitante = $resultados['goles_visitante'] ?? null;

            if (! is_numeric($golesLocal) || ! is_numeric($golesVisitante) || $golesLocal < 0 || $golesVisitante < 0) {
                throw new \InvalidArgumentException('Invalid scores provided');
            }

            // Update goals and status
            $juego->equipo_local_goles = $golesLocal;
            $juego->equipo_visitante_goles = $golesVisitante;
            $juego->estado = 'Finalizado';

            return $juego->save();
        });
    }

    public function actualizarResultados(int $id, int $golesLocal, int $golesVisitante): bool
    {
        $juego = $this->find($id);

        if (! $juego) {
            return false;
        }

        // Update goals only if game is not finalized or if we're updating finalized game
        $juego->equipo_local_goles = $golesLocal;
        $juego->equipo_visitante_goles = $golesVisitante;

        return $juego->save();
    }

    public function getJuegosPorEtapaConResultados(int $etapaId): Collection
    {
        return $this->model->where('etapa_id', $etapaId)
            ->with(['equipoLocal', 'equipoVisitante', 'etapa', 'predicciones'])
            ->orderBy('fecha_hora')
            ->get();
    }
}
