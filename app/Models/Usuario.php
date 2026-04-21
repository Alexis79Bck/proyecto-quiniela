<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'nombre_completo',
    'nombre_usuario',
    'correo_electronico',
    'password',
    'correo_verificado',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Obtener las quinielas a las que pertenece el usuario.
     *
     * @return BelongsToMany<Quiniela>
     */
    public function quinielas(): BelongsToMany
    {
        return $this->belongsToMany(Quiniela::class, 'quiniela_usuario');
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

    /**
     * Verificar si el usuario tiene autenticación de dos factores habilitada.
     */
    public function tiene2FAHabilitado(): bool
    {
        return ! is_null($this->two_factor_secret) && 
               ! is_null($this->two_factor_confirmed_at);
    }

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\UserFactory::new();
    }
}