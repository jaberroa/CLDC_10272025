<?php

namespace App\Http\Requests\Miembros;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMiembroRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar un miembro.
     */
    public function rules(): array
    {
        $miembroId = $this->route('id');

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('miembros', 'email')->ignore($miembroId),
            ],
            'cedula' => [
                'required',
                'string',
                'max:255',
                Rule::unique('miembros', 'cedula')->ignore($miembroId),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'profesion' => ['nullable', 'string', 'max:255'],
            'tipo_membresia' => ['required', Rule::in(['fundador', 'activo', 'pasivo', 'honorifico', 'estudiante', 'diaspora'])],
            'estado_membresia' => ['required', Rule::in(['activa', 'suspendida', 'inactiva', 'honoraria'])],
            'organizacion_id' => ['required', 'exists:organizaciones,id'],
            'fecha_ingreso' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Normaliza los datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => $this->nombre ? trim($this->nombre) : $this->nombre,
            'apellido' => $this->apellido ? trim($this->apellido) : $this->apellido,
            'email' => $this->email ? trim($this->email) : $this->email,
            'cedula' => $this->cedula ? trim($this->cedula) : $this->cedula,
        ]);
    }

    /**
     * Devuelve los datos validados, anexando nombre completo.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['nombre_completo'] = trim(($validated['nombre'] ?? '') . ' ' . ($validated['apellido'] ?? ''));

        return $validated;
    }
}
