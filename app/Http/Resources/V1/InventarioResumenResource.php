<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioResumenResource extends JsonResource
{
    /**
     * @param  array<string, mixed>  $resource
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'sku' => $this->resource['sku'],
            'nombre' => $this->resource['nombre'],
            'tipo' => $this->resource['tipo'],
            'unidadMedida' => $this->resource['unidadMedida'],
            'gravaIva' => $this->resource['gravaIva'],
            'stockMinimo' => $this->resource['stockMinimo'],
            'precioSugerido' => $this->resource['precioSugerido'],
            'proveedorId' => $this->resource['proveedorId'],
            'proveedorNombre' => $this->resource['proveedorNombre'] ?? null,
            'cuentaContableVentaId' => $this->resource['cuentaContableVentaId'],
            'sucursalId' => $this->resource['sucursalId'],
            'stock' => $this->resource['stock'],
            'stockBajo' => $this->resource['stockBajo'],
            'controlaStock' => $this->resource['controlaStock'] ?? true,
        ];
    }
}
