<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\DocumentoResource;

class ClienteResource extends JsonResource
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
            'nombreRazonSocial' => $this->nombre_razon_social,
            'documentoIdentidad' => $this->rif_ci,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'tipo' => $this->tipo,
            'limiteCredito' => $this->limite_credito,
            'diasCredito' => $this->dias_credito,
            'saldo' => $this->saldo,
            'documentoFiscal' => DocumentoResource::collection($this->whenLoaded('documentoFiscal')),

        ];
    }
}
