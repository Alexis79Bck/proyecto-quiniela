<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre',
])]
class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    // Relaciones
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class);
    }
}
