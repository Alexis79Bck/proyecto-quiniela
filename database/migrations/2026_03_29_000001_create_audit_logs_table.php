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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('accion'); // login, logout, create_prediction, update_score, etc.
            $table->string('tipo_entidad')->nullable(); // quiniela, match, prediction, user, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable(); // Soporta IPv6
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Información adicional
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index('usuario_id');
            $table->index('accion');
            $table->index(['tipo_entidad', 'entity_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
