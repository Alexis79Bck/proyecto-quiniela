<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FinalizarJuegoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your auth requirements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'goles_local.required' => 'El campo goles local es requerido.',
            'goles_local.integer' => 'El campo goles local debe ser un número entero.',
            'goles_local.min' => 'El campo goles local debe ser mayor o igual a 0.',
            'goles_visitante.required' => 'El campo goles visitante es requerido.',
            'goles_visitante.integer' => 'El campo goles visitante debe ser un número entero.',
            'goles_visitante.min' => 'El campo goles visitante debe ser mayor o igual a 0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        //
    }
}
