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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->integer('home_prediction');
            $table->integer('away_prediction');
            $table->integer('points_earned')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->boolean('bonus_enabled')->default(false);
            $table->timestamps();

            // Unique constraint to prevent duplicate predictions
            $table->unique(['user_id', 'match_id']);

            // Add indexes for better performance
            $table->index('user_id');
            $table->index('match_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};