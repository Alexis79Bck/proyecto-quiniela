<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Juego;
use App\Models\Etapa;
use App\Models\Equipo;
use App\Models\Usuario;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JuegoControllerTest extends TestCase
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
        
        // Create and authenticate a test user
        $this->user = Usuario::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_juegos()
    {
        // Create test games
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->getJson('/api/juegos');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'etapa_id',
                    'equipo_local_id',
                    'equipo_visitante_id',
                    'fecha_hora',
                    'estado'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_can_create_juego()
    {
        $data = [
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay()->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/juegos', $data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'etapa_id',
                'equipo_local_id',
                'equipo_visitante_id',
                'fecha_hora',
                'estado'
            ]
        ]);
        
        $this->assertDatabaseHas('juegos', [
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'estado' => 'Programado'
        ]);
    }

    /** @test */
    public function it_can_get_specific_juego()
    {
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->getJson("/api/juegos/{$juego->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'etapa_id',
                'equipo_local_id',
                'equipo_visitante_id',
                'fecha_hora',
                'estado'
            ]
        ]);
    }

    /** @test */
    public function it_can_update_juego()
    {
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $data = [
            'fecha_hora' => now()->addDays(2)->toDateTimeString(),
        ];
        
        $response = $this->putJson("/api/juegos/{$juego->id}", $data);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'fecha_hora'
            ]
        ]);
        
        $this->assertDatabaseHas('juegos', [
            'id' => $juego->id,
            'fecha_hora' => $data['fecha_hora']
        ]);
    }

    /** @test */
    public function it_can_delete_juego()
    {
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->deleteJson("/api/juegos/{$juego->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        
        $this->assertSoftDeleted('juegos', ['id' => $juego->id]);
    }

    /** @test */
    public function it_can_start_juego()
    {
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subHour(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->postJson("/api/juegos/{$juego->id}/iniciar");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        
        $this->assertDatabaseHas('juegos', [
            'id' => $juego->id,
            'estado' => 'En Progreso'
        ]);
    }

    /** @test */
    public function it_can_finish_juego()
    {
        $juego = Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->subHour(),
            'estado' => 'En Progreso'
        ]);
        
        $data = [
            'goles_local' => 2,
            'goles_visitante' => 1
        ];
        
        $response = $this->postJson("/api/juegos/{$juego->id}/finalizar", $data);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
        
        $this->assertDatabaseHas('juegos', [
            'id' => $juego->id,
            'estado' => 'Finalizado',
            'equipo_local_goles' => 2,
            'equipo_visitante_goles' => 1
        ]);
    }

    /** @test */
    public function it_can_get_calendario()
    {
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->getJson('/api/juegos/calendario');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'etapa_id',
                    'equipo_local_id',
                    'equipo_visitante_id',
                    'fecha_hora',
                    'estado'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_can_get_proximos_juegos()
    {
        Juego::factory()->create([
            'etapa_id' => $this->etapa->id,
            'equipo_local_id' => $this->equipoLocal->id,
            'equipo_visitante_id' => $this->equipoVisitante->id,
            'fecha_hora' => now()->addDay(),
            'estado' => 'Programado'
        ]);
        
        $response = $this->getJson('/api/juegos/proximos');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'etapa_id',
                    'equipo_local_id',
                    'equipo_visitante_id',
                    'fecha_hora',
                    'estado'
                ]
            ]
        ]);
    }
}