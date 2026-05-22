<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo' => ['required', 'string', 'max:64'],
            'cxcId' => ['nullable', 'integer', 'exists:cuenta_por_cobrar,id'],
            'referenciaBancaria' => ['nullable', 'string', 'max:255'],
            'banco' => ['nullable', 'string', 'max:255'],
            'fecha' => ['nullable', 'date'],
        ];
    }
}
