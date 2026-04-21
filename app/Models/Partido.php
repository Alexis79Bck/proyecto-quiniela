<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Etapa;
use App\Models\Equipo;
use App\Models\Prediccion;
use App\Models\Puntaje;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    // Relaciones
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    public function equipoLocal(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_local');
    }

    public function equipoVisitante(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante');
    }

    public function predicciones(): HasMany
    {
        return $this->hasMany(Prediccion::class);
    }

    public function puntajes(): HasMany
    {
        return $this->hasMany(Puntaje::class);
    }
}