<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinalizarJuegoRequest;
use App\Http\Requests\IniciarJuegoRequest;
use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Services\JuegoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LogicException;
use ModelNotFoundException;

class JuegoController extends Controller
{
    public function __construct(
        private JuegoService $juegoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get query parameters for filtering
            $estado = $request->query('estado');
            $etapaId = $request->query('etapa_id');

            if ($estado) {
                $juegos = $this->juegoService->obtenerPorEstado($estado);
            } elseif ($etapaId) {
                $juegos = $this->juegoService->obtenerPorEtapa((int) $etapaId);
            } else {
                $juegos = $this->juegoService->obtenerCalendario();
            }

            return response()->json([
                'success' => true,
                'data' => $juegos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request): JsonResponse
    {
        try {
            $juego = $this->juegoService->crearJuego($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Juego creado exitosamente',
                'data' => $juego,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $juego = $this->juegoService->juegoRepository->getWithPredictions($id);

            if (! $juego) {
                return response()->json([
                    'success' => false,
                    'message' => 'Juego no encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $juego,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuegoRequest $request, int $id): JsonResponse
    {
        try {
            $juego = $this->juegoService->actualizarJuego($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Juego actualizado exitosamente',
                'data' => $juego,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        } catch (LogicException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->juegoService->eliminarJuego($id);

            if (! $result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Juego no encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Juego eliminado exitosamente',
            ]);
        } catch (LogicException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start the game (transition from PROGRAMADO to EN_PROGRESO).
     */
    public function iniciar(IniciarJuegoRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->juegoService->iniciarJuego($id);

            if (! $result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Juego no encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Juego iniciado exitosamente',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        } catch (LogicException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Finish the game (record results and transition to FINALIZADO).
     */
    public function finalizar(FinalizarJuegoRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->juegoService->finalizarJuego($id, $request->validated());

            if (! $result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Juego no encontrado',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Juego finalizado exitosamente',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Juego no encontrado',
            ], 404);
        } catch (LogicException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get complete calendar grouped by stages.
     */
    public function calendario(): JsonResponse
    {
        try {
            $calendario = $this->juegoService->obtenerCalendario();

            return response()->json([
                'success' => true,
                'data' => $calendario,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get upcoming games.
     */
    public function proximos(Request $request): JsonResponse
    {
        try {
            $limite = $request->query('limite', 10);
            $proximos = $this->juegoService->obtenerProximos((int) $limite);

            return response()->json([
                'success' => true,
                'data' => $proximos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
