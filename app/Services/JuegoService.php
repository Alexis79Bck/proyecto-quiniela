<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Events\JuegoFinalizado;
use App\Events\JuegoIniciado;
use App\Models\Juego;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class JuegoService
{
    public function __construct(
        private JuegoRepositoryInterface $juegoRepository,
        private PredictionService $predictionService
    ) {}

    public function crearJuego(array $data): Juego
    {
        // Validate required fields
        $required = ['etapa_id', 'equipo_local_id', 'equipo_visitante_id', 'fecha_hora'];
        foreach ($required as $field) {
            if (! isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate teams are not the same
        if ($data['equipo_local_id'] === $data['equipo_visitante_id']) {
            throw new \InvalidArgumentException('Local and visiting teams cannot be the same');
        }

        // Set initial state as PROGRAMADO
        $data['estado'] = MatchStatus::PROGRAMADO->value;

        // Create the game
        return $this->juegoRepository->create($data);
    }

    public function actualizarJuego(int $id, array $data): Juego
    {
        $juego = $this->juegoRepository->find($id);

        if (! $juego) {
            throw new \ModelNotFoundException("Juego not found with id: {$id}");
        }

        // Prevent updating results if game is finalized
        if ($juego->estado === MatchStatus::FINALIZADO->value) {
            throw new \LogicException('Cannot update a finalized game');
        }

        // Update only allowed fields
        $allowedFields = ['etapa_id', 'equipo_local_id', 'equipo_visitante_id', 'fecha_hora'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        $this->juegoRepository->update($id, $updateData);

        return $this->juegoRepository->find($id);
    }

    public function eliminarJuego(int $id): bool
    {
        $juego = $this->juegoRepository->find($id);

        if (! $juego) {
            throw new \ModelNotFoundException("Juego not found with id: {$id}");
        }

        // Check if game has predictions
        if ($juego->predicciones->count() > 0) {
            throw new \LogicException('Cannot delete a game that has predictions');
        }

        return $this->juegoRepository->delete($id);
    }

    public function iniciarJuego(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $juego = $this->juegoRepository->find($id);

            if (! $juego) {
                throw new \ModelNotFoundException("Juego not found with id: {$id}");
            }

            // Validate game is in PROGRAMADO state
            if ($juego->estado !== MatchStatus::PROGRAMADO->value) {
                throw new \LogicException('Only programmed games can be started');
            }

            // Validate game time is in the past or present
            if ($juego->fecha_hora->isFuture()) {
                throw new \LogicException('Cannot start a game in the future');
            }

            // Update state to EN_PROGRESO
            $juego->estado = MatchStatus::EN_PROGRESO->value;
            $juego->save();

            // Emit event
            Event::dispatch(new JuegoIniciado($juego));

            return true;
        });
    }

    public function finalizarJuego(int $id, array $resultados): bool
    {
        return DB::transaction(function () use ($id, $resultados) {
            // Validate results
            $golesLocal = $resultados['goles_local'] ?? null;
            $golesVisitante = $resultados['goles_visitante'] ?? null;

            if (! is_numeric($golesLocal) || ! is_numeric($golesVisitante) || $golesLocal < 0 || $golesVisitante < 0) {
                throw new \InvalidArgumentException('Invalid scores provided');
            }

            $juego = $this->juegoRepository->find($id);

            if (! $juego) {
                throw new \ModelNotFoundException("Juego not found with id: {$id}");
            }

            // Validate game is not already finalized
            if ($juego->estado === MatchStatus::FINALIZADO->value) {
                throw new \LogicException('Game is already finalized');
            }

            // Update goals
            $juego->equipo_local_goles = $golesLocal;
            $juego->equipo_visitante_goles = $golesVisitante;

            // Set state to FINALIZADO
            $juego->estado = MatchStatus::FINALIZADO->value;
            $juego->save();

            // Process predictions for scoring
            $this->procesarResultados($juego);

            // Emit event
            Event::dispatch(new JuegoFinalizado($juego));

            return true;
        });
    }

    public function obtenerCalendario(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->juegoRepository->model
            ->with(['etapa', 'equipoLocal', 'equipoVisitante'])
            ->orderBy('fecha_hora')
            ->get();
    }

    public function obtenerPorEstado(string $estado): Collection
    {
        return $this->juegoRepository->getByStatus($estado);
    }

    public function obtenerPorEtapa(int $etapaId): Collection
    {
        return $this->juegoRepository->getByStage($etapaId);
    }

    public function obtenerProximos(int $limite = 10): Collection
    {
        return $this->juegoRepository->model
            ->where('estado', '!=', MatchStatus::FINALIZADO->value)
            ->where('fecha_hora', '>', now())
            ->with(['equipoLocal', 'equipoVisitante', 'etapa'])
            ->orderBy('fecha_hora')
            ->take($limite)
            ->get();
    }

    public function procesarResultados(Juego $juego): void
    {
        // Validate game is finalized
        if ($juego->estado !== MatchStatus::FINALIZADO->value) {
            throw new \LogicException('Cannot process results for non-finalized game');
        }

        // Get all predictions for this game
        $predicciones = $juego->predicciones;

        // Calculate points for each prediction
        foreach ($predicciones as $prediccion) {
            $puntos = $this->predictionService->calculateScore($prediccion);

            // Update puntos_obtenidos field
            $prediccion->puntos_obtenidos = $puntos;
            $prediccion->save();
        }
    }
}
