<?php

namespace App\Http\Requests\Directivas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDirectivaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'miembro_id' => 'required|exists:miembros,id',
            'organo_id' => 'required|exists:organos,id',
            'cargo_id' => 'required|exists:cargos,id',
            'periodo_directiva' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'estado' => 'required|in:activo,inactivo,suspendido',
            'observaciones' => 'nullable|string|max:1000',
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
            'miembro_id.required' => 'El miembro es obligatorio.',
            'miembro_id.exists' => 'El miembro seleccionado no existe.',
            'organo_id.required' => 'El órgano es obligatorio.',
            'organo_id.exists' => 'El órgano seleccionado no existe.',
            'cargo_id.required' => 'El cargo es obligatorio.',
            'cargo_id.exists' => 'El cargo seleccionado no existe.',
            'periodo_directiva.max' => 'El período de directiva no puede exceder los 255 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo, inactivo o suspendido.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 1000 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'miembro_id' => 'miembro',
            'organo_id' => 'órgano',
            'cargo_id' => 'cargo',
            'periodo_directiva' => 'período de directiva',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'estado' => 'estado',
            'observaciones' => 'observaciones',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir estado a minúsculas
        if ($this->has('estado')) {
            $this->merge([
                'estado' => strtolower($this->estado)
            ]);
        }
    }
}