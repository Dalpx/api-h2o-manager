<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoInventarioDetalleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'itemId' => $this->item_id,
            'itemNombre' => $this->whenLoaded('item', fn () => $this->item->nombre),
            'itemSku' => $this->whenLoaded('item', fn () => $this->item->sku),
            'cantidad' => (float) $this->cantidad,
            'signo' => (int) $this->signo,
            'costoUnitario' => (float) $this->costo_unitario,
            'motivo' => $this->motivo,
        ];
    }
}
