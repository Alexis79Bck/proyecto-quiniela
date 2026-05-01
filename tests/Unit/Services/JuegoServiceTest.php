<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Juego;
use App\Models\Etapa;
use App\Models\Equipo;
use App\Models\Prediccion;
use App\Models\Usuario;
use App\Services\JuegoService;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use App\Services\PredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class JuegoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->etapa = Etapa::factory()->create();
        $this->equipoLocal = Equipo::factory()->create([
            'nombre' => 'Equipo Local',
            'codigo_fifa' => 'LOC'
        ]);
        $this->equipoVisitante = Equipo::factory()->create([
            'nombre' => 'Equipo Visitante',
            'codigo_fifa' => 'VIS'
        ]);
        
        // Mock dependencies
        $this->mockJuegoRepository = Mockery::mock(JuegoRepositoryInterface::class);
        $this->mockPredictionService = Mockery::mock(PredictionService::class);
        
        $this->juegoService = new JuegoService(
            $this->mockJuegoRepository,
            $this->mockPredictionService
        );
    }

    public function test_it_can_create_juego(): void
    {
        $data = [
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
        ];
        
        $this->mockJuegoRepository->shouldReceive('create')
            ->with($data + ['estado' => 'Programado'])
            ->andReturn(Juego::factory()->make([
                'id' => 1,
                'etapa_id' => $this->etapa->id,
                'equipo_local_id' => $this->equipoLocal->id,
                'equipo_visitante_id' => $this->equipoVisitante->id,
                'fecha_hora' => now()->addDay(),
                'estado' => 'Programado'
            ]));
        
        $juego = $this->juegoService->crearJuego($data);
        
        $this->assertEquals('Programado', $juego->estado);
        $this->assertEquals($this->etapa->id, $juego->etapa_id);
    }

    public function test_it_can_iniciar_juego(): void
    {
        $juegoId = 1;
        $juego = Juego::factory()->make([
            'id' => $juegoId,
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subHour(),
            'estado' => 'Programado'
        ]);
        
        $this->mockJuegoRepository->shouldReceive('find')
            ->with($juegoId)
            ->andReturn($juego);
        
        $mockJuegoForUpdate = Mockery::mock(Juego::class);
        $mockJuegoForUpdate->shouldReceive('save')->andReturnTrue();
        $this->mockJuegoRepository->shouldReceive('find')
            ->with($juegoId)
            ->andReturn($mockJuegoForUpdate);
        
        $result = $this->juegoService->iniciarJuego($juegoId);
        
        $this->assertTrue($result);
        $this->mockJuegoRepository->shouldHaveReceived('find')->twice();
    }

    public function test_it_can_finalizar_juego(): void
    {
        $juegoId = 1;
        $juego = Juego::factory()->make([
            'id' => $juegoId,
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subHour(),
            'estado' => 'En Progreso'
        ]);
        
        $this->mockJuegoRepository->shouldReceive('find')
            ->with($juegoId)
            ->andReturn($juego);
        
        $mockJuegoForUpdate = Mockery::mock(Juego::class);
        $mockJuegoForUpdate->shouldReceive('save')->andReturnTrue();
        $this->mockJuegoRepository->shouldReceive('find')
            ->with($juegoId)
            ->andReturn($mockJuegoForUpdate);
        
        $this->mockPredictionService->shouldReceive('calculateScore')
            ->times(2)
            ->andReturn(5);
        
        $result = $this->juegoService->finalizarJuego($juegoId, [
            'goles_local' => 2,
            'goles_visitante' => 1
        ]);
        
        $this->assertTrue($result);
    }

    public function test_it_can_obtener_juegos_por_estado(): void
    {
        $estado = 'Programado';
        $expectedJuegos = collect([Juego::factory()->make()]);
        
        $this->mockJuegoRepository->shouldReceive('getByStatus')
            ->with($estado)
            ->andReturn($expectedJuegos);
        
        $juegos = $this->juegoService->obtenerPorEstado($estado);
        
        $this->assertEquals($expectedJuegos, $juegos);
    }
}