<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ProveedorQuery extends ApiFilter{

    protected $allowedParams = [
        'razonSocial' => ['eq', 'ne', 'like'],
        'rif' => ['eq', 'ne', 'like'],
        'contacto' => ['eq', 'ne', 'like'],
        'telefono' => ['eq', 'ne'],
        'createdAt' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'updatedAt' => ['eq', 'gt', 'lt', 'gte', 'lte']
    ];

    protected $columnMap = [
        'razonSocial' => 'razon_social',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'like' => 'like'
    ];

}