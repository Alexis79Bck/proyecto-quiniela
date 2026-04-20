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
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('numero_juego');
            $table->foreignId('etapa_id')->constrained('etapas')->onDelete('cascade');
            $table->foreignId('equipo1_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo2_id')->constrained('equipos')->onDelete('cascade');
            $table->dateTime('fecha_juego');
            $table->integer('equipo1_goles')->nullable();
            $table->integer('equipo2_goles')->nullable();
            $table->enum('estado', ['programado', 'en_progreso', 'finalizado'])->default('programado');
            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index('etapa_id');
            $table->index('equipo1_id');
            $table->index('equipo2_id');
            $table->index('fecha_juego');
            $table->index('estado');
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