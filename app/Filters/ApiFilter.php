<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{

    protected $allowedParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];


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