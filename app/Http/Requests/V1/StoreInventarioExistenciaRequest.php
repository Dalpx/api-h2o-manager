<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventarioExistenciaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'sucursalId' => ['required', 'integer', 'exists:sucursal,id'],
            'itemId' => ['required', 'integer', 'exists:item,id'],
            'cantidadActual' => ['required', 'numeric'],
        ];
    }
}