<?php

namespace App\Http\Resources\V1;

use App\Models\Cliente;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Http\Resources\V1\DocumentoDetalleResource;

class DocumentoResource extends JsonResource
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
            'id'=> $this->id,
            'sucursal' => $this->sucursal->nombre ?? null,
            'tipoDoc'=> $this->tipo_doc,
            'serieCorrelativo'=> $this->serie_correlativo,
            'fecha'=> $this->fecha,
            'cliente' => $this->cliente->nombre_razon_social ?? null,
            'condicionesPago' => $this->condiciones_pago,
            'subtotal' => $this->subtotal,
            'iva'=> $this->iva,
            'total'=> $this->subtotal + $this->iva,
            'estado'=> $this->estado,
            'createdAt'=> $this->created_at->format('d/m/Y H:i:s'),
            'updatedAt'=> $this->updated_at?->format('d/m/Y H:i:s'),
            'detalles' => DocumentoDetalleResource::collection($this->whenLoaded('detalles')),


        ];
    }
}
