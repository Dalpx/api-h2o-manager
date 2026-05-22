<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AjustarInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sucursalId' => ['required', 'integer', 'exists:sucursal,id'],
            'itemId' => ['required', 'integer', 'exists:item,id'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'direccion' => ['required', Rule::in(['entrada', 'salida'])],
            'usuarioId' => ['required', 'integer', 'exists:usuario,id'],
            'motivo' => ['nullable', 'string', 'max:500'],
            'referenciaDoc' => ['nullable', 'string', 'max:255'],
            'fecha' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
