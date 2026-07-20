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
        // Tabla de etapas: lista las fases o rondas de una competición, como grupos, semifinales, finales, etc.
        Schema::create('etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
            $table->string('external_id')->unique();
            $table->string('proveedor');
            $table->string('nombre');
           // $table->unsignedInteger('orden')->default(0); // Para ordenar las etapas dentro de la temporada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapas');
    }
};
