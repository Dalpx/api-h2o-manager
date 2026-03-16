<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class UserQuery extends ApiFilter{

    protected $allowedParams = [
        'nombre' => ['eq'],
        'email' => ['eq'],
        'cedula' => ['eq'],
        'rol' => ['eq'],
        'sucursal' => ['eq', 'ne'],
        'createdAt' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'updatedAt' => ['eq', 'gt', 'lt', 'gte', 'lte']
    ];

    protected $columnMap = [
    'createdAt' => 'created_at',
    'updatedAt' => 'updated_at'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<='
    ];

}