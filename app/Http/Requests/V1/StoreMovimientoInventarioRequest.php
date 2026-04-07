<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovimientoInventarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha'         => ['required', 'date_format:Y-m-d H:i:s'],
            'sucursalId'    => ['required', 'integer', 'exists:sucursal,id'],
            'tipo'          => ['required', 'string', Rule::in(['compra', 'venta', 'ajuste', 'traslado', 'merma'])],
            'referenciaDoc' => ['nullable', 'string', 'max:255'],
            'usuarioId'     => ['required', 'integer', 'exists:usuario,id'], // Asumiendo que la tabla es users
        ];
    }
}