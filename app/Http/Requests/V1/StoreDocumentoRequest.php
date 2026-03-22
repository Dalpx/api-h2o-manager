<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentoRequest extends FormRequest
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
        return [
            'sucursalId' => ['required', 'integer', 'exists:sucursal,id'],
            'tipoDoc' => ['required', 'string', Rule::in('Factura','Nota de Crédito')],
            'serieCorrelativo' => ['required', 'string', 'max:255', 'unique:documento_fiscal,serie_correlativo'],
            'fecha' => ['required', 'date_format:Y-m-d H:i:s'],
            'clienteId' => ['required', 'integer', 'exists:cliente,id'],
            'condicionesPago' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'iva' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'sucursalId.required' => 'La sucursal es obligatoria.',
            'sucursalId.integer' => 'El ID de la sucursal debe ser un número entero.',
            'sucursalId.exists' => 'La sucursal seleccionada no existe.',

            'tipoDoc.required' => 'El tipo de documento es obligatorio.',
            'tipoDoc.in' => 'El tipo de documento debe ser: Factura o Nota de Crédito.',

            'serieCorrelativo.required' => 'La serie y el correlativo son obligatorios.',
            'serieCorrelativo.unique' => 'Este correlativo ya está registrado',

            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener el formato: Año-Mes-Día Hora:Minuto:Segundo.',

            'clienteId.required' => 'El cliente es obligatorio.',
            'clienteId.exists' => 'El cliente seleccionado no existe.',

            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un valor numérico.',

            'iva.required' => 'El IVA es obligatorio.',
            'total.required' => 'El total es obligatorio.',
            'total.min' => 'El total no puede ser negativo.',

            'estado.required' => 'El estado de la factura es obligatorio.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'sucursal_id' => $this->postalCode 
        ]);
    }
}
