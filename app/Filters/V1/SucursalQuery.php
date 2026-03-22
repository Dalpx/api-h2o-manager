<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class SucursalQuery extends ApiFilter
{
    /**
     * Parámetros CamelCase permitidos y sus operadores SQL válidos.
     */
    protected $allowedParams = [
        'id'               => ['eq', 'ne'],
        'nombre'           => ['eq', 'ne'],
        'rif'              => ['eq', 'ne'],
        'direccion'        => ['eq', 'ne'],
        'createdAt'        => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'updatedAt'        => ['eq', 'gt', 'lt', 'gte', 'lte'],
    ];

    /**
     * Mapeo de parámetros CamelCase del request a columnas snake_case de la DB.
     */
    protected $columnMap = [
        'createdAt'        => 'created_at',
        'updatedAt'        => 'updated_at',
        // No mapeamos correlativosDoc porque es JSON, requiere filtros avanzados
    ];

    /**
     * Mapeo de operadores abreviados a operadores SQL reales.
     */
    protected $operatorMap = [
        'eq'  => '=',
        'ne'  => '!=',
        'gt'  => '>',
        'gte' => '>=',
        'lt'  => '<',
        'lte' => '<=',
    ];
}