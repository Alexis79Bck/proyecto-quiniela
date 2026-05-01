<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJuegoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming only authenticated users can create games
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
            'etapa_id' => 'required|integer|exists:etapas,id',
            'equipo_local_id' => 'required|integer|exists:equipos,id',
            'equipo_visitante_id' => 'required|integer|exists:equipos,id',
            'fecha_hora' => 'required|date|after_or_equal:now',
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
            'fecha_hora.after_or_equal' => 'La fecha y hora debe ser igual o posterior a la actual.',
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
