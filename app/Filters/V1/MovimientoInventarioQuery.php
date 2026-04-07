<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class MovimientoInventarioQuery extends ApiFilter {

    protected $allowedParams = [
        'fecha'         => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'sucursalId'    => ['eq'],
        'tipo'          => ['eq'],
        'referenciaDoc' => ['eq', 'like'],
        'usuarioId'     => ['eq'],
    ];

    protected $columnMap = [
        'sucursalId'    => 'sucursal_id',
        'referenciaDoc' => 'referencia_doc',
        'usuarioId'     => 'usuario_id',
    ];

    protected $operatorMap = [
        'eq'   => '=',
        'lt'   => '<',
        'lte'  => '<=',
        'gt'   => '>',
        'gte'  => '>=',
        'like' => 'like'
    ];
}