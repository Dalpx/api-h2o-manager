<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 1. Obtenemos el ID de la sucursal de la ruta para la validación del nombre único
        $sucursal = $this->route('sucursal');
        $sucursalId = $sucursal instanceof \App\Models\Sucursal ? $sucursal->id : $sucursal;

        $method = $this->method();

        // 2. Definimos las reglas base
        $rules = [
            'nombre' => [
                'string', 
                'max:255', 
                // Único en tabla 'sucursal', columna 'nombre', ignorando el ID actual
                Rule::unique('sucursal', 'nombre')->ignore($sucursalId)
            ],
            'rif'             => ['string', 'max:255'],
            'direccion'       => ['nullable', 'string'],
            'correlativosDoc' => ['array'],
        ];

        // 3. Si es PUT, todos los campos (excepto dirección) son obligatorios
        if ($method === 'PUT') {
            $rules['nombre'][] = 'required';
            $rules['rif'][]    = 'required';
            $rules['correlativosDoc'][] = 'required';
            return $rules;
        }

        // 4. Si es PATCH, aplicamos 'sometimes' a todas las reglas
        // Esto permite enviar SOLO el campo que quieres cambiar sin que el RIF chille
        return array_map(function($ruleArray) {
            return array_merge(['sometimes'], $ruleArray);
        }, $rules);
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra sucursal con ese nombre.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'rif.string'    => 'El RIF debe ser un texto válido.',
        ];
    }
}