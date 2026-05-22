<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CuentaPorCobrarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $doc = $this->whenLoaded('documento');

        return [
            'id' => $this->id,
            'clienteId' => $this->cliente_id,
            'docId' => $this->doc_id,
            'fecha' => $this->fecha,
            'vencimiento' => $this->vencimiento,
            'saldo' => (float) $this->saldo,
            'estado' => $this->estado,
            'serieCorrelativo' => $doc?->serie_correlativo,
            'totalFactura' => $doc ? (float) $doc->total : null,
        ];
    }
}
