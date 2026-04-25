<?php

namespace App\Infrastructure\Auth\Fortify;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): Usuario
    {
        Validator::make($input, [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'nombre_usuario' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Usuario::class),
            ],
            'correo_electronico' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Usuario::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return Usuario::create([
            'nombre_completo' => $input['nombre_completo'],
            'nombre_usuario' => $input['nombre_usuario'],
            'correo_electronico' => $input['correo_electronico'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
