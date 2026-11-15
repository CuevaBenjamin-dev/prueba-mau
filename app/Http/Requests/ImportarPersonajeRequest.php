<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarPersonajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['id' => ['required', 'integer', 'between:1,83']];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Ingresa un ID de SWAPI.',
            'id.integer' => 'El ID debe ser un número entero.',
            'id.between' => 'El ID debe estar entre 1 y 83.',
        ];
    }
}
