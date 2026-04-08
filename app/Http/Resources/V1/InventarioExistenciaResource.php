<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioExistenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sucursalId' => $this->sucursal_id,
            'sucursalNombre' => $this->sucursal ? $this->sucursal->nombre : 'N/A',
            'itemId' => $this->item_id,
            'itemNombre' => $this->item ? $this->item->nombre : 'N/A',
            'itemSku' => $this->item ? $this->item->sku : 'N/A',
            'cantidadActual' => $this->cantidad_actual,
        ];
    }
}