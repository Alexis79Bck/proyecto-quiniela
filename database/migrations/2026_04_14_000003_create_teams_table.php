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
        // Tabla de equipos: guarda la información de cada equipo, su proveedor de datos,
        // nombres, código, país y recursos de emblema/ banderas.
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('proveedor');
            $table->string('nombre');
            $table->string('nombre_corto')->nullable();
            $table->string('codigo', 3)->unique();
            $table->string('emblema_url')->nullable();
            $table->string('pais')->nullable();
            $table->string('bandera_url')->nullable();
            $table->unsignedInteger('anio_fundado')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
