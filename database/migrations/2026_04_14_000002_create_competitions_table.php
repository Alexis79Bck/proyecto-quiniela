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
        // Tabla de competiciones: registra las competiciones o torneos con datos de proveedor,
        // nombre, tipo y área geográfica.
        Schema::create('competiciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('proveedor');
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->string('tipo');
            $table->string('emblema_url')->nullable();
            $table->boolean('es_activa')->default(true);
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competiciones');
    }
};
