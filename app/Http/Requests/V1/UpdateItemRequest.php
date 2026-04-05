<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();
        $itemId = $this->route('item') ? $this->route('item')->id : null;

        $baseRules = [
            'sku' => ['string', Rule::unique('item', 'sku')->ignore($itemId)->whereNull('deleted_at')],
            'nombre' => ['string', 'max:255'],
            'tipo' => [Rule::in(['PRODUCTO', 'SERVICIO'])],
            'unidadMedida' => ['string', 'max:10'],
            'gravaIva' => ['boolean'],
            'proveedorId' => ['integer', 'exists:proveedor,id'],
            'stockMinimo' => ['numeric', 'min:0'],
            'precioSugerido' => ['numeric', 'min:0'],
            'cuentaContableVentaId' => [
                Rule::exists('cuenta_contable', 'id')->where(function ($query) {
                    $query->whereIn('tipo', ['INGRESO', 'CUENTA_POR_COBRAR']);
                }),
            ],
        ];

        if ($method === 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        }

        return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
    }
}
