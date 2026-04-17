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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->integer('exact_results')->default(0);
            $table->integer('correct_winners')->default(0);
            $table->integer('correct_team_goals')->default(0);
            $table->integer('total_goals')->default(0);
            $table->integer('points_earned')->default(0);
            $table->timestamps();

            // Unique constraint to prevent duplicate scores
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
        Schema::dropIfExists('scores');
    }
};