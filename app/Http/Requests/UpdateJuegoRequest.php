<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJuegoRequest extends FormRequest
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
            'etapa_id' => 'sometimes|integer|exists:etapas,id',
            'equipo_local_id' => 'sometimes|integer|exists:equipos,id',
            'equipo_visitante_id' => 'sometimes|integer|exists:equipos,id',
            'fecha_hora' => 'sometimes|date',
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
            'equipo_local_id.exists' => 'El equipo local seleccionado no existe.',
            'equipo_visitante_id.exists' => 'El equipo visitante seleccionado no existe.',
            'etapa_id.exists' => 'La etapa seleccionada no existe.',
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
