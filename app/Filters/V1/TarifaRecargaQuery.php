<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class TarifaRecargaQuery extends ApiFilter {

    protected $allowedParams = [
        'tamanoId' => ['eq'],
        'sucursalId' => ['eq'],
        'creadoPor' => ['eq'],
        'precio' => ['eq', 'gt', 'lt', 'gte', 'lte'],
    ];

    protected $columnMap = [
        'tamanoId' => 'tamano_id',
        'sucursalId' => 'sucursal_id',
        'creadoPor' => 'creado_por',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}