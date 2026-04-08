<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class InventarioExistenciaQuery extends ApiFilter {

    protected $allowedParams = [
        'sucursalId' => ['eq'],
        'itemId' => ['eq'],
        'cantidadActual' => ['eq', 'gt', 'lt', 'gte', 'lte'],
    ];

    protected $columnMap = [
        'sucursalId' => 'sucursal_id',
        'itemId' => 'item_id',
        'cantidadActual' => 'cantidad_actual',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}