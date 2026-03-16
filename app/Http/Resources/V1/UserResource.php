<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'nombre'=> $this->nombre,
            'email'=> $this->email,
            'cedula'=> $this->cedula,
            'rol' => $this->rol->nombre,
            'sucursal' => $this->sucursal->nombre ?? null,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt'=> $this->updated_at->format('Y-m-d H:i:s'),
            'deletedAt'=> $this->deleted_at?->format('Y-m-d H:i:s') ?? null,

        ];
    }
}
