<?php

namespace App\Services\V1;

use App\Models\Item;
use Illuminate\Support\Carbon;

class ItemService
{
    public function store(array $data)
    {
        return Item::create($this->transform($data, now()));
    }

    public function update(Item $item, array $data)
    {
        $mappedData = $this->transform($data);
        $item->update($mappedData);
        return $item->fresh();
    }

    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        $res = [];
        $fields = [
            'sku'                   => 'sku',
            'nombre'                => 'nombre',
            'tipo'                  => 'tipo',
            'unidadMedida'          => 'unidad_medida',
            'gravaIva'              => 'grava_iva',
            'stockMinimo'           => 'stock_minimo',
            'precioSugerido'        => 'precio_sugerido',
            'proveedorId'           => 'proveedor_id',              // <--- Clave para el error
            'cuentaContableVentaId' => 'cuenta_contable_venta_id'   // <--- Clave para el error
        ];

        foreach ($fields as $jsonKey => $dbKey) {
            if (isset($data[$jsonKey])) {
                $res[$dbKey] = $data[$jsonKey];
            }
        }

        if ($timestamp !== null) {
            $res['created_at'] = $timestamp;
            $res['updated_at'] = $timestamp;
        }

        return $res;
    }
}
