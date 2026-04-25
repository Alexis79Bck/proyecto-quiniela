<?php

namespace App\Infrastructure\Auth\Fortify;

use App\Models\Usuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(Usuario $user, array $input): void
    {
        Validator::make($input, [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'correo_electronico' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios')->ignore($user->id, 'id'),
            ],
        ])->validateWithBag('updateProfileInformation');

        if ($input['correo_electronico'] !== $user->correo_electronico &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'nombre_completo' => $input['nombre_completo'],
                'correo_electronico' => $input['correo_electronico'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(Usuario $user, array $input): void
    {
        $user->forceFill([
            'nombre_completo' => $input['nombre_completo'],
            'correo_electronico' => $input['correo_electronico'],
            'correo_verificado' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
