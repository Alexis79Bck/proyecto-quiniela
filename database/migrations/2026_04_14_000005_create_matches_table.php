<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de juegos: define los partidos, su calendario, estado, resultados y relaciones
        // con equipos, temporada, etapa y grupo.
        Schema::create('juegos', function (Blueprint $table) {
            $table->id(); //Primary Key de la tabla

            $table->unsignedBigInteger('external_id')->unique(); //ID externo del juego, único para cada juego. Relacionado al Proveedor de datos (e.g., football-data.org)
            $table->string('proveedor'); //Proveedor de datos (e.g., football-data.org)

            // Relaciones con otras tablas
            $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
            $table->foreignId('etapa_id')->constrained('etapas')->onDelete('cascade');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();

            // Atributos de esta tabla
            $table->foreignId('equipo_local_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo_visitante_id')->constrained('equipos')->onDelete('cascade');
            $table->unsignedSmallInteger('jornada')->nullable();
            $table->dateTime('fecha_hora');
            $table->dateTime('fecha_cierre_predicciones');

            // Equipo ganador (si el juego ha finalizado)
            $table->foreignId('equipo_ganador_id')->nullable()->constrained('equipos')->nullOnDelete();

            // Resultado en Tiempo Reglamentario
            $table->unsignedInteger('equipo_local_goles')->default(0);
            $table->unsignedInteger('equipo_visitante_goles')->default(0);

            // Resultado en Prorroga (si aplica)
            $table->unsignedInteger('equipo_local_goles_prorroga')->nullable();
            $table->unsignedInteger('equipo_visitante_goles_prorroga')->nullable();

            // Resultado en Penales (si aplica)
            $table->unsignedInteger('equipo_local_goles_penales')->nullable();
            $table->unsignedInteger('equipo_visitante_goles_penales')->nullable();

            // Estado del juego
            $table->string('estado')->default('programado'); // programado, en_progreso, finalizado

            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index('etapa_id');
            $table->index('equipo_local_id');
            $table->index('equipo_visitante_id');
            $table->index('fecha_hora');
            $table->index('estado');
            $table->index('temporada_id');
            $table->index('grupo_id');
            $table->index(['etapa_id', 'grupo_id']); // Para consultas que filtren por etapa y grupo
            $table->index(['temporada_id', 'estado', 'fecha_hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juegos');
    }
};
