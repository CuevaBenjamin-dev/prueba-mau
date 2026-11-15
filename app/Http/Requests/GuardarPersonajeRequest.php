<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarPersonajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
            'altura' => ['nullable', 'integer', 'between:1,300'],
            'masa' => ['nullable', 'integer', 'between:1,500'],
            'color_cabello' => ['nullable', 'string', 'max:100'],
            'color_ojos' => ['nullable', 'string', 'max:100'],
            'genero' => ['required', Rule::in(['masculino', 'femenino', 'n/a'])],
            'planeta_origen' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'color_cabello' => 'color de cabello',
            'color_ojos' => 'color de ojos',
            'genero' => 'género',
            'planeta_origen' => 'planeta de origen',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max.string' => 'El campo :attribute no puede superar :max caracteres.',
            'between.numeric' => 'El campo :attribute debe estar entre :min y :max.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }
}
