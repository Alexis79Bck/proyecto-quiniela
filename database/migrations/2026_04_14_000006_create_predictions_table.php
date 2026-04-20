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
        Schema::create('predicciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('juego_id')->constrained('juegos')->onDelete('cascade');
            $table->integer('equipo1_prediccion')->nullable();
            $table->integer('equipo2_prediccion')->nullable();
            $table->integer('puntos_obtenidos')->default(0);
            $table->boolean('esta_bloqueado')->default(false);
            $table->timestamps();

            // Restricción única para evitar predicciones duplicadas
            $table->unique(['usuario_id', 'juego_id']);

            // Índices para mejor rendimiento
            $table->index('usuario_id');
            $table->index('juego_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predicciones');
    }
};