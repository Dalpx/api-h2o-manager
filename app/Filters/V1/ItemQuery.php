<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ItemQuery extends ApiFilter {

    protected $allowedParams = [
        'sku' => ['eq', 'like'],
        'nombre' => ['eq'],
        'tipo' => ['eq'],
        'unidadMedida' => ['eq'],
        'gravaIva' => ['eq'],
        'precioSugerido' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'cuentaContableVentaId' => ['eq']
    ];

    protected $columnMap = [
        'unidadMedida' => 'unidad_medida',
        'gravaIva' => 'grava_iva',
        'precioSugerido' => 'precio_sugerido',
        'cuentaContableVenta' => 'cuenta_contable_venta_id'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}