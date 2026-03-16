<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ClienteQuery extends ApiFilter{

    protected $allowedParams = [
        'nombreRazonSocial' => ['eq'],
        'documentoIdentidad' => ['eq'],
        'telefono' => ['eq'],
        'direccion' => ['eq'],
        'tipo' => ['eq', 'ne'],
        'limiteCredito' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'diasCredito' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'saldo' => ['eq', 'gt', 'lt', 'gte', 'lte']
    ];

    protected $columnMap = [
    'nombreRazonSocial' => 'nombre_razon_social',
    'documentoIdentidad' => 'rif_ci',
    'limiteCredito' => 'limite_credito',
    'diasCredito' => 'dias_credito',
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