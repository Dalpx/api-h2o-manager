<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['nullable', 'date'],
            'origen' => ['nullable', 'string', 'max:64'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'sucursalId' => ['nullable', 'integer', 'exists:sucursal,id'],
            'detalles' => ['required', 'array', 'min:2'],
            'detalles.*.cuentaId' => ['required', 'integer', 'exists:cuenta_contable,id'],
            'detalles.*.debe' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.haber' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
