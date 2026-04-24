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
            $table->foreignId('etapa_id')->constrained('etapas')->onDelete('cascade');
            $table->foreignId('equipo_local_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo_visitante_id')->constrained('equipos')->onDelete('cascade');
            $table->datetime('fecha_hora');
            $table->unsignedInteger('equipo_local_goles')->default(0);
            $table->unsignedInteger('equipo_visitante_goles')->default(0);
            $table->string('estado')->default('programado'); // programado, en_progreso, finalizado
            // $table->enum('estado', ['programado', 'en_progreso', 'finalizado'])->default('programado');
            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index('etapa_id');
            $table->index('equipo_local_id');
            $table->index('equipo_visitante_id');
            $table->index('fecha_hora');
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
