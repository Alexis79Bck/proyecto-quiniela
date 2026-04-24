<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre',
    'codigo_fifa',
    'url_bandera',
    'grupo_id',
])]
class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    // Relaciones
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function juegosComoLocal(): HasMany
    {
        return $this->hasMany(Juego::class, 'equipo_local_id');
    }

    public function juegosComoVisitante(): HasMany
    {
        return $this->hasMany(Juego::class, 'equipo_visitante_id');
    }
}
