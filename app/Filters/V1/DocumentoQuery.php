<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class DocumentoQuery extends ApiFilter{

    protected $allowedParams = [
        'tipoDoc' => ['eq'],
        'serieCorrelativo' => ['eq'],
        'fecha' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'condicionesPago' => ['eq', 'ne'],
        'subtotal' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'createdAt' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'updatedAt' => ['eq', 'gt', 'lt', 'gte', 'lte']
    ];

    protected $columnMap = [
    'tipoDoc' => 'tipo_doc',
    'serieCorrelativo' => 'serie_correlativo',
    'condicionesPago' => 'condiciones_pago',
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


    public function Transform(Request $request)
    {
        $eloQuery = [];

        foreach ($this->allowedParams as $param => $operat) {
            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operat as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
}