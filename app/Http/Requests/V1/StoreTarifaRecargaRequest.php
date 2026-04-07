<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarifaRecargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tamanoId'   => ['required', 'integer', 'exists:tamano_recarga,id'],
            'precio'     => ['required', 'numeric', 'min:0'],
            'sucursalId' => ['nullable', 'integer', 'exists:sucursal,id'],
            'creadoPor'  => ['required', 'integer', 'exists:usuario,id'],
        ];
    }
}
