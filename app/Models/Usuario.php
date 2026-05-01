<?php

namespace App\Models;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'nombre_completo',
    'nombre_usuario',
    'correo_electronico',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasApiTokens, HasFactory, HasRoles;

    protected $table = 'usuarios';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'correo_verificado' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtener las predicciones del usuario.
     *
     * @return HasMany<Prediccion>
     */
    public function predicciones(): HasMany
    {
        return $this->hasMany(Prediccion::class);
    }
}
