<?php

namespace App\Http\Requests\Miembros;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMiembroRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un miembro.
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            // Forzar conexión pgsql en las reglas unique
            'email' => ['nullable', 'email', 'max:255', 'unique:pgsql.miembros,email'],
            'cedula' => ['required', 'string', 'max:255', 'unique:pgsql.miembros,cedula'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'profesion' => ['nullable', 'string', 'max:255'],
            // Forzar conexión pgsql en exists
            'estado_membresia_id' => ['required', 'exists:pgsql.estados_membresia,id'],
            'tipo_membresia' => ['required', 'string', 'in:fundador,activo,pasivo,honorifico,estudiante,diaspora'],
            'organizacion_id' => ['required', 'exists:pgsql.organizaciones,id'],
            'fecha_ingreso' => ['required', 'date'],
            // 5MB = 5120 KB
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
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
