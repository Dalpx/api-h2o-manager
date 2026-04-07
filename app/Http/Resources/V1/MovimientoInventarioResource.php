<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoInventarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'fecha'         => $this->fecha,
            'tipo'          => $this->tipo,
            'referenciaDoc' => $this->referencia_doc,
            
            'sucursalId'    => $this->sucursal_id,
            'sucursalNombre'=> $this->sucursal ? $this->sucursal->nombre : 'Desconocida',
            
            'usuarioId'     => $this->usuario_id,
            'usuarioNombre' => $this->usuario ? $this->usuario->nombre : 'Desconocido', // o 'username' según tu tabla
            
            'createdAt'     => $this->created_at,
        ];
    }
}