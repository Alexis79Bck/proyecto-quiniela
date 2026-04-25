<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre',
    'descripcion',
])]
class Etapa extends Model
{
    use HasFactory;

    protected $table = 'etapas';

    // Relaciones
    public function juegos(): HasMany
    {
        return $this->hasMany(Juego::class);
    }
}
