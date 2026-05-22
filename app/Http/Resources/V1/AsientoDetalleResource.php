<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsientoDetalleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asientoId' => $this->asiento_id,
            'cuentaId' => $this->cuenta_id,
            'cuentaCodigo' => $this->whenLoaded('cuenta', fn () => $this->cuenta->codigo),
            'cuentaNombre' => $this->whenLoaded('cuenta', fn () => $this->cuenta->nombre),
            'cuentaTipo' => $this->whenLoaded('cuenta', fn () => $this->cuenta->tipo),
            'debe' => (float) $this->debe,
            'haber' => (float) $this->haber,
        ];
    }
}
