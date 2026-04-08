<?php

namespace App\Services\V1;

use App\Models\InventarioExistencia;

class InventarioExistenciaService
{
    public function updateOrCreate(array $data)
    {
        // Al ser llave compuesta, usamos updateOrCreate de Eloquent
        return InventarioExistencia::updateOrCreate(
            [
                'sucursal_id' => $data['sucursalId'],
                'item_id' => $data['itemId']
            ],
            [
                'cantidad_actual' => $data['cantidadActual']
            ]
        );
    }

    public function transform(array $data): array
    {
        $res = [];
        $fields = [
            'sucursalId' => 'sucursal_id',
            'itemId' => 'item_id',
            'cantidadActual' => 'cantidad_actual',
        ];

        foreach ($fields as $jsonKey => $dbKey) {
            if (isset($data[$jsonKey])) {
                $res[$dbKey] = $data[$jsonKey];
            }
        }
        return $res;
    }
}