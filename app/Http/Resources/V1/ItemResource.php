<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'unidadMedida' => $this->unidad_medida,
            'gravaIva' => (bool)$this->grava_iva,
            'stockMinimo' => $this->stock_minimo,
            'precioSugerido' => $this->precio_sugerido,
            'proveedorId' => $this->proveedor ? $this->proveedor->razon_social : 'Null', 
            'cuentaContableVenta' => $this->cuentaContableVenta ? $this->cuentaContableVenta->nombre : 'Sin asignar',
            'deletedAt' => $this->deleted_at
        ];
    }
}
