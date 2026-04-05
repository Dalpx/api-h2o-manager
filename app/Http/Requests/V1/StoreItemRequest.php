<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', Rule::unique('item', 'sku')->whereNull('deleted_at')],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['PRODUCTO', 'SERVICIO'])],
            'unidadMedida' => ['required', 'string', 'max:10'],
            'gravaIva' => ['required', 'boolean'],
            'proveedorId' => ['required', 'integer', 'exists:proveedor,id'],
            'stockMinimo' => ['numeric', 'min:0'],
            'precioSugerido' => ['required', 'numeric', 'min:0'],
            'cuentaContableVentaId' => [
                'required',
                // Validamos que la cuenta exista y que sea de tipo ingreso/venta
                Rule::exists('cuenta_contable', 'id')->where(function ($query) {
                    $query->whereIn('tipo', ['Activo', 'activo', 'Ingreso', 'ingreso']); // Ajusta según tus nombres reales en DB
                }),
            ],
        ];
    }
}
