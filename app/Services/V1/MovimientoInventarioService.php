<?php

namespace App\Services\V1;

use App\Models\MovimientoInventario;
use Illuminate\Support\Carbon;

class MovimientoInventarioService
{
    public function store(array $data)
    {
        return MovimientoInventario::create($this->transform($data, now()));
    }

    public function update(MovimientoInventario $movimiento, array $data)
    {
        $mappedData = $this->transform($data);
        unset($mappedData['created_at'], $mappedData['updated_at']);

        $movimiento->update($mappedData);
        return $movimiento->fresh();
    }

    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        $res = [];
        $fields = [
            'fecha'         => 'fecha',
            'sucursalId'    => 'sucursal_id',
            'tipo'          => 'tipo',
            'referenciaDoc' => 'referencia_doc',
            'usuarioId'     => 'usuario_id',
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