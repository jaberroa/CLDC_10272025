<?php

namespace App\Http\Requests\Organizaciones;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizacionRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:organizaciones,codigo',
            'tipo' => 'required|string|in:nacional,seccional,seccional_internacional,diaspora',
            'estado' => 'nullable|string|in:activa,inactiva,suspendida',
            'descripcion' => 'nullable|string|max:1000',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
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
            'nombre.required' => 'El nombre de la organización es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'codigo.required' => 'El código de la organización es obligatorio.',
            'codigo.unique' => 'Este código ya está siendo utilizado por otra organización.',
            'codigo.max' => 'El código no puede exceder los 50 caracteres.',
            'tipo.required' => 'El tipo de organización es obligatorio.',
            'tipo.in' => 'El tipo de organización seleccionado no es válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'descripcion.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'direccion.max' => 'La dirección no puede exceder los 500 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'email.email' => 'Debe proporcionar una dirección de correo electrónico válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen válida.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'logo.max' => 'La imagen no puede ser mayor a 2MB.',
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
            'nombre' => 'nombre de la organización',
            'codigo' => 'código de la organización',
            'tipo' => 'tipo de organización',
            'estado' => 'estado',
            'descripcion' => 'descripción',
            'direccion' => 'dirección',
            'telefono' => 'teléfono',
            'email' => 'correo electrónico',
            'logo' => 'logo',
        ];
    }
}

