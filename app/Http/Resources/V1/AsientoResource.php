<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $debe = (float) ($this->total_debe ?? $this->detalles?->sum('debe') ?? 0);
        $haber = (float) ($this->total_haber ?? $this->detalles?->sum('haber') ?? 0);

        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'origen' => $this->origen,
            'referencia' => $this->referencia,
            'sucursalId' => $this->sucursal_id,
            'totalDebe' => $debe,
            'totalHaber' => $haber,
            'detalles' => AsientoDetalleResource::collection($this->whenLoaded('detalles')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
