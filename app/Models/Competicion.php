<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competicion extends Model
{
    protected $fillable = [
        'external_id',
        'nombre',
        'codigo',
        'tipo',
        'emblema_url',
        'area_nombre',
        'area_codigo',
        'es_activa'
    ];

    
}
