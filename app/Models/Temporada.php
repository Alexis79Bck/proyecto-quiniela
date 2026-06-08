<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $fillable = [
        'external_id',
        'competicion_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'esta_activa'
    ];
}
