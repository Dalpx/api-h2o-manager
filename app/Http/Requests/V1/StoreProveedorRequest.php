<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razonSocial' => ['required', 'string', 'max:255'],
            'rif'         => ['required', 'string', Rule::unique('proveedor', 'rif')->whereNull('deleted_at'), 'regex:/^[JGVEP][-]?\d{8,9}[-]?\d$/i'],
            'contacto'    => ['required', 'string'],
            'telefono'    => ['required', 'string'],
            'direccion'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'razonSocial.required' => 'La razón social es obligatoria.',
            'rif.required' => 'El RIF es obligatorio.',
            'rif.unique' => 'Este RIF ya está registrado para otro proveedor activo.',
            'rif.regex' => 'El formato del RIF no es válido.',
        ];
    }
}
