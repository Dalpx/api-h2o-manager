<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarifaRecargaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'precio' => $this->precio,
            'tamanoId' => $this->tamano_id,
            'tamanoNombre' => $this->tamano ? $this->tamano->nombre : 'N/A',
            'sucursalId' => $this->sucursal_id,
            'sucursalNombre' => $this->sucursal ? $this->sucursal->nombre : 'Global',
            'creadoPorId' => $this->creado_por,
            'creadoPor' => $this->usuario ? $this->usuario->nombre : 'N/A',
            'createdAt' => $this->created_at,
        ];
    }
}