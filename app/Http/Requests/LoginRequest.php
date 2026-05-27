<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'correo_electronico' => [
                'required',
                function ($attribute, $value, $fail) {
                    // If it's a valid email, pass.
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return;
                    }
                    // Otherwise validate as username: alphanumeric, 8-15 chars.
                    if (! preg_match('/^[a-zA-Z0-9]{8,15}$/', $value)) {
                        $fail('El campo correo electrónico debe ser un email válido o un nombre de usuario alfanumérico (8‑15 caracteres).');
                    }
                },
            ],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'correo_electronico.required' => 'El correo electrónico o nombre de usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $firstError = $errors->first();
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $firstError,
                'data' => null,
            ], 422)
        );
    }
}
