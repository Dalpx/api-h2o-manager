<?php

namespace App\Services\V1;

use App\Models\Proveedor;
use Illuminate\Support\Carbon;

class ProveedorService
{
    /**
     * Crea un nuevo proveedor mapeando los campos del JSON.
     */
    public function store(array $data)
    {
        $mappedData = $this->transform($data, now());
        return Proveedor::create($mappedData);
    }

    /**
     * Actualiza un proveedor existente.
     */
    public function update(Proveedor $proveedor, array $data)
    {
        $mappedData = $this->transform($data);
        
        // Evitamos sobreescribir timestamps manualmente si no es necesario
        unset($mappedData['created_at'], $mappedData['updated_at']);

        $proveedor->update($mappedData);
        return $proveedor->fresh();
    }

    /**
     * Traduce las llaves de CamelCase (API) a SnakeCase (DB).
     */
    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        $res = [];

        // Mapeo condicional para permitir actualizaciones parciales (PATCH)
        if (isset($data['razonSocial'])) $res['razon_social'] = $data['razonSocial'];
        if (isset($data['rif']))         $res['rif']          = $data['rif'];
        if (isset($data['contacto']))    $res['contacto']     = $data['contacto'];
        if (isset($data['telefono']))    $res['telefono']     = $data['telefono'];
        if (isset($data['direccion']))   $res['direccion']    = $data['direccion'];

        if ($timestamp !== null) {
            $res['created_at'] = $timestamp;
            $res['updated_at'] = $timestamp;
        }

        return $res;
    }
}