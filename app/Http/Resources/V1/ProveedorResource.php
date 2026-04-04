<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'razonSocial' => $this->razon_social,
            'rif' => $this->rif,
            'contacto' => $this->contacto,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'deletedAt' => $this->when($this->deleted_at != null, $this->deleted_at),
        ];
    }
}