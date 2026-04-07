<?php

namespace App\Services\V1;

use App\Models\TarifaRecarga;
use Illuminate\Support\Carbon;

class TarifaRecargaService
{
    public function store(array $data)
    {
        return TarifaRecarga::create($this->transform($data, now()));
    }

    public function update(TarifaRecarga $tarifa, array $data)
    {
        $mappedData = $this->transform($data);
        unset($mappedData['created_at'], $mappedData['updated_at']);
        
        $tarifa->update($mappedData);
        return $tarifa->fresh();
    }

    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        $res = [];
        $fields = [
            'tamanoId'   => 'tamano_id',
            'precio'     => 'precio',
            'sucursalId' => 'sucursal_id',
            'creadoPor'  => 'creado_por',
        ];

        foreach ($fields as $jsonKey => $dbKey) {
            if (isset($data[$jsonKey])) {
                $res[$dbKey] = $data[$jsonKey];
            }
        }

        if ($timestamp) {
            $res['created_at'] = $timestamp;
            $res['updated_at'] = $timestamp;
        }

        return $res;
    }
}