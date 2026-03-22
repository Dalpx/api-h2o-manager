<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkStoreDocumentoRequest extends FormRequest
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
            'facturas' => ['required', 'array', 'min:1', 'max:100'],
            'facturas.*.sucursalId' => ['required', 'integer', 'exists:sucursal,id'],
            'facturas.*.tipoDoc' => ['required', 'string', 'in:Factura,Nota de Crédito'],
            'facturas.*.serieCorrelativo' => ['required', 'string', 'max:255', 'unique:documento_fiscal,serie_correlativo'],
            'facturas.*.fecha' => ['required', 'date_format:Y-m-d H:i:s'],
            'facturas.*.clienteId' => ['required', 'integer', 'exists:cliente,id'],
            'facturas.*.condicionesPago' => ['required', 'string', 'max:255'],
            'facturas.*.subtotal' => ['required', 'numeric', 'min:0'],
            'facturas.*.iva' => ['required', 'numeric', 'min:0'],
            'facturas.*.total' => ['required', 'numeric', 'min:0'],
            'facturas.*.estado' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'facturas.required' => 'El listado de facturas es obligatorio.',
            'facturas.array' => 'El formato del lote debe ser un arreglo.',
            'facturas.min' => 'Debes enviar al menos una factura.',
            'facturas.max' => 'No puedes procesar más de 100 facturas por lote.',

            'facturas.*.sucursalId.required' => 'La sucursal es obligatoria.',
            'facturas.*.sucursalId.integer' => 'El ID de la sucursal debe ser un número entero.',
            'facturas.*.sucursalId.exists' => 'La sucursal seleccionada no existe.',

            'facturas.*.tipoDoc.required' => 'El tipo de documento es obligatorio.',
            'facturas.*.tipoDoc.in' => 'El tipo de documento debe ser: Factura o Nota de Crédito.',

            'facturas.*.serieCorrelativo.required' => 'La serie y el correlativo son obligatorios.',

            'facturas.*.fecha.required' => 'La fecha es obligatoria.',
            'facturas.*.fecha.date_format' => 'La fecha debe tener el formato: Año-Mes-Día Hora:Minuto:Segundo.',

            'facturas.*.clienteId.required' => 'El cliente es obligatorio.',
            'facturas.*.clienteId.exists' => 'El cliente seleccionado no existe.',

            'facturas.*.subtotal.required' => 'El subtotal es obligatorio.',
            'facturas.*.subtotal.numeric' => 'El subtotal debe ser un valor numérico.',

            'facturas.*.iva.required' => 'El IVA es obligatorio.',
            'facturas.*.total.required' => 'El total es obligatorio.',
            'facturas.*.total.min' => 'El total no puede ser negativo.',

            'facturas.*.estado.required' => 'El estado de la factura es obligatorio.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'facturas.*.sucursal_id' => $this->postalCode
        ]);
    }
}
