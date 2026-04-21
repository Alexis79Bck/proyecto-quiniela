<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Grupo;
use App\Models\Partido;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    // Relaciones
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function partidosComoLocal(): HasMany
    {
        return $this->hasMany(Partido::class, 'equipo_local');
    }

    public function partidosComoVisitante(): HasMany
    {
        return $this->hasMany(Partido::class, 'equipo_visitante');
    }
}