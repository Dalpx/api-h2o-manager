<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SucursalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'nombre'          => $this->nombre,
            'rif'             => $this->rif,
            'direccion'       => $this->direccion,
            // Gracias al cast en el modelo, esto ya es un array de PHP
            'correlativosDoc' => $this->correlativos_doc, 
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,
        ];
    }
}