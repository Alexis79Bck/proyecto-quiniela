<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Partido;

class Etapa extends Model
{
    use HasFactory;

    protected $table = 'etapas';

    // Relaciones
    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class);
    }
}