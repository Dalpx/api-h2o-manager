<?php

namespace App\Services\V1;

use App\Models\Sucursal;
use Illuminate\Support\Carbon;

class SucursalService
{
    public function store(array $data)
    {
        $mappedData = $this->transform($data, now());
        return Sucursal::create($mappedData);
    }

    public function update(Sucursal $sucursal, array $data)
    {
        $mappedData = $this->transform($data);

        // IMPORTANTE: Eliminamos las llaves que sean NULL para que no 
        // sobreescriba datos existentes con nulos en un PATCH
        $mappedData = array_filter($mappedData, fn($value) => !is_null($value));

        $sucursal->update($mappedData);
        return $sucursal->fresh();
    }

    public function destroy(Sucursal $sucursal)
    {
        return $sucursal->delete();
    }

    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        // Usamos ?? null para evitar el error de "Undefined array key"
        $res = [
            'nombre'           => $data['nombre'] ?? null,
            'rif'              => $data['rif'] ?? null,
            'direccion'        => $data['direccion'] ?? null,
            'correlativos_doc' => $data['correlativosDoc'] ?? null,
        ];

        if ($timestamp !== null) {
            $res['created_at'] = $timestamp;
            $res['updated_at'] = $timestamp;
        }

        return $res;
    }
}
