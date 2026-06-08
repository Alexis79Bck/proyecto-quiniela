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
        Schema::create('competiciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('proveedor');
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->string('tipo');
            $table->string('emblema_url')->nullable();
            $table->string('area_nombre')->nullable();
            $table->string('area_codigo')->nullable();
            $table->boolean('es_activa')->default(true);
            $table->timestamps();
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
