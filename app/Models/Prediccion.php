<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Usuario;
use App\Models\Juego;

class Prediccion extends Model
{
    use HasFactory;

    protected $table = 'predicciones';

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class);
    }
}