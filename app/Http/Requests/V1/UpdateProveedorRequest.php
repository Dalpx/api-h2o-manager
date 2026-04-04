<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $method = $this->method();
        // Se obtiene el ID del proveedor desde la ruta para ignorarlo en la validación de unicidad
        $proveedorId = $this->route('proveedor') ? $this->route('proveedor')->id : null;

        $baseRules = [
            'razonSocial' => ['string', 'max:255'],
            'rif'         => [
                'string',
                'max:255',
                // Ignora el ID actual y verifica que no esté marcado como eliminado (Soft Delete)
                Rule::unique('proveedor', 'rif')->ignore($proveedorId)->whereNull('deleted_at'),
                'regex:/^[JGVEP][-]?\d{8,9}[-]?\d$/i'
            ],
            'contacto'    => ['string', 'max:255'],
            'telefono'    => ['string', 'max:255'],
            'direccion'   => ['string'],
        ];

        if ($method === 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        }

        return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
    }

    public function messages(): array
    {
        return [
            'rif.regex' => 'El formato del RIF es inválido. Debe seguir el patrón: J-12345678-0.',
            'rif.unique' => 'Este RIF ya se encuentra registrado en el sistema.',
        ];
    }
}
