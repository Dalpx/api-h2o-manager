<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoDetalleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //Con el archivo de recursos formateamos y retornamos el JSON
        return [
            'id' => $this->id,
            'itemId' => $this->item_id,
            'cantidad' => $this->cantidad,
            'precioUnitario' => $this->precio_unit,
            'ivaMonto' => $this->iva_monto,
            'totalLineas' => $this->total_linea,
            // Puedes incluir el nombre del ítem si cargas la relación
            'nombreItem' => $this->item->nombre ?? null, 
        
        ];
    }
}
