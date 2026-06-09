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
        // Tabla de equipos_temporadas: enlaza equipos con temporadas y grupos asignados.
        Schema::create('equipos_temporadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->nullable()->onDelete('set null');
            $table->timestamps();

            $table->index(['equipo_id', 'temporada_id']); // Un equipo solo puede estar una vez por temporada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos_temporadas');
    }
};
