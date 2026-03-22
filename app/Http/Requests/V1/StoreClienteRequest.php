<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombreRazonSocial' => ['required', 'string', 'max:255'],
            'rifCi'             => ['required', 'string', 'max:255', 'unique:cliente,rif_ci'],
            'telefono'          => ['nullable', 'string', 'max:255'],
            'direccion'         => ['nullable', 'string'],
            'tipo'              => ['required', 'string', 'in:Natural,Jurídico'], // Basado en el comentario de tu DB
            'limiteCredito'     => ['nullable', 'numeric', 'min:0'],
            'diasCredito'       => ['nullable', 'integer', 'min:0'],
            'saldo'             => ['nullable', 'numeric'],
        ];
    }
}
