<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        $docId = $this->route('documentoFiscal') ? $this->route('documentoFiscal')->id : null;

        $baseRules = [
            'sucursalId'       => ['integer', 'exists:sucursal,id'],
            'tipoDoc'          => ['string', 'in:Factura,Nota de Crédito'],
            'serieCorrelativo' => ['string', 'max:255', Rule::unique('documento_fiscal', 'serie_correlativo')->ignore($docId)],
            'fecha'            => ['date_format:Y-m-d H:i:s'],
            'clienteId'        => ['integer', 'exists:cliente,id'],
            'condicionesPago'  => ['string', 'max:255'],
            'subtotal'         => ['numeric', 'min:0'],
            'iva'              => ['numeric', 'min:0'],
            'total'            => ['numeric', 'min:0'],
            'estado'           => ['string', 'max:255'],
        ];

        if ($method === 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        }

        return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
    }
}
