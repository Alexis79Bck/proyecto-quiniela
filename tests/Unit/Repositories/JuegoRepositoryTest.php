<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Juego;
use App\Models\Etapa;
use App\Models\Equipo;
use App\Repositories\Eloquent\JuegoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JuegoRepositoryTest extends TestCase
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
    }

    public function test_can_get_programados_juegos(): void
    {
        $repository = new JuegoRepository(new Juego);
        
        // Create a programado game
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        // Create a finalizado game
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subDay(),
            'estado' => 'Finalizado',
            'equipo_local_goles' => 2,
            'equipo_visitante_goles' => 1
        ]);
        
        $programados = $repository->getProgramados();
        
        $this->assertCount(1, $programados);
        $this->assertEquals('Programado', $programados->first()->estado->value);
    }

    public function test_can_get_en_progreso_juegos(): void
    {
        $repository = new JuegoRepository(new Juego);
        
        // Create an en progreso game
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subHour(),
            'estado' => 'En Progreso'
        ]);
        
        $enProgreso = $repository->getEnProgreso();
        
        $this->assertCount(1, $enProgreso);
        $this->assertEquals('En Progreso', $enProgreso->first()->estado->value);
    }

    public function test_can_get_finalizados_juegos(): void
    {
        $repository = new JuegoRepository(new Juego);
        
        // Create a finalizado game
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subDay(),
            'estado' => 'Finalizado',
            'equipo_local_goles' => 2,
            'equipo_visitante_goles' => 1
        ]);
        
        $finalizados = $repository->getFinalizados();
        
        $this->assertCount(1, $finalizados);
        $this->assertEquals('Finalizado', $finalizados->first()->estado->value);
    }

    public function test_can_finalizar_juego(): void
    {
        $repository = new JuegoRepository(new Juego);
        
        // Create a game to finalize
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $result = $repository->finalizarJuego($juego->id, [
            'goles_local' => 2,
            'goles_visitante' => 1
        ]);
        
        $this->assertTrue($result);
        
        $juego->refresh();
        $this->assertEquals('Finalizado', $juego->estado->value);
        $this->assertEquals(2, $juego->equipo_local_goles);
        $this->assertEquals(1, $juego->equipo_visitante_goles);
    }

    public function test_can_actualizar_resultados(): void
    {
        $repository = new JuegoRepository(new Juego);
        
        // Create a game
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $result = $repository->actualizarResultados($juego->id, 3, 2);
        
        $this->assertTrue($result);
        
        $juego->refresh();
        $this->assertEquals(3, $juego->equipo_local_goles);
        $this->assertEquals(2, $juego->equipo_visitante_goles);
    }
}