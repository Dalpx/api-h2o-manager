<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $clienteId = $cliente instanceof \App\Models\Cliente ? $cliente->id : $cliente;

        $rules = [
            'nombreRazonSocial' => ['string', 'max:255'],
            'rifCi'             => [
                'string', 
                'max:255', 
                Rule::unique('cliente', 'rif_ci')->ignore($clienteId)
            ],
            'telefono'          => ['nullable', 'string', 'max:255'],
            'direccion'         => ['nullable', 'string'],
            'tipo'              => ['string', 'in:Natural,Jurídico'],
            'limiteCredito'     => ['numeric', 'min:0'],
            'diasCredito'       => ['integer', 'min:0'],
            'saldo'             => ['numeric'],
        ];

        // Si es PUT, hacemos los campos no nulos requeridos
        if ($this->isMethod('put')) {
            $rules['nombreRazonSocial'][] = 'required';
            $rules['rifCi'][] = 'required';
            $rules['tipo'][] = 'required';
            return $rules;
        }

        // Si es PATCH, usamos sometimes
        return array_map(fn($rule) => array_merge(['sometimes'], $rule), $rules);
    }
}
