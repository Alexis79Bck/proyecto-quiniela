<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Etapa;
use App\Models\Equipo;
use App\Models\Prediccion;

#[Fillable([
    'uuid',
    'etapa_id',
    'equipo_local_id',
    'equipo_visitante_id',
    'fecha_hora',
    'equipo_local_goles',
    'equipo_visitante_goles',
    'estado',
])]
class Juego extends Model
{
    use HasFactory;

    protected $table = 'juegos';

    protected $casts = [
        'fecha_hora' => 'datetime',
        'estado' => MatchStatus::class,
    ];

    // Relaciones
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    public function equipoLocal(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function predicciones(): HasMany
    {
        return $this->hasMany(Prediccion::class);
    }

}
