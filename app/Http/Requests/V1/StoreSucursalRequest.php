<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitimos la acción
    }

    public function rules(): array
    {
        return [
            'nombre'          => ['required', 'string', 'max:255', 'unique:sucursal,nombre'],
            'rif'             => ['required', 'string', 'max:255'],
            'direccion'       => ['nullable', 'string'],
            // Validamos que sea un array/JSON
            'correlativosDoc' => ['required', 'array'],
        ];
    }

    /**
     * Mensajes de error personalizados para las validaciones.
     */
    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre de la sucursal es obligatorio.',
            'nombre.string'            => 'El nombre debe ser una cadena de texto válida.',
            'nombre.max'               => 'El nombre no puede exceder los 255 caracteres.',
            'nombre.unique'            => 'Este nombre ya lo tiene otra sucursal',
            'rif.required'             => 'El RIF es obligatorio para la sucursal.',
            'rif.string'               => 'El RIF debe ser un texto válido.',
            'direccion.string'         => 'La dirección debe ser una descripción de texto.',
            'correlativosDoc.required' => 'Es necesario definir los correlativos iniciales (JSON).',
            'correlativosDoc.array'    => 'Los correlativos deben ser un objeto válido (array).',
        ];
    }
}
