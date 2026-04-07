<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovimientoInventarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $method = $this->method();

        $baseRules = [
            'fecha'         => ['date_format:Y-m-d H:i:s'],
            'sucursalId'    => ['integer', 'exists:sucursal,id'],
            'tipo'          => ['string', Rule::in(['compra', 'venta', 'ajuste', 'traslado', 'merma'])],
            'referenciaDoc' => ['nullable', 'string', 'max:255'],
            'usuarioId'     => ['integer', 'exists:usuario,id'],
        ];

        if ($method === 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        }

        return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
    }
}