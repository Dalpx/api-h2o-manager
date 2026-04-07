<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTarifaRecargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();
        $baseRules = [
            'tamanoId'   => ['integer', 'exists:tamano_recarga,id'],
            'precio'     => ['numeric', 'min:0'],
            'sucursalId' => ['nullable', 'integer', 'exists:sucursal,id'],
            'creadoPor'  => ['integer', 'exists:usuario,id'],
        ];

        if ($method === 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        }

        return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
    }
}
