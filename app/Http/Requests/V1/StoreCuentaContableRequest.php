<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCuentaContableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipos = ['Activo', 'Pasivo', 'Patrimonio', 'Ingreso', 'Egreso'];

        return [
            'codigo' => ['required', 'string', 'max:32', 'unique:cuenta_contable,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in($tipos)],
        ];
    }
}
