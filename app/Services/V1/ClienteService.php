<?php

namespace App\Services\V1;

use App\Models\Cliente;

class ClienteService
{
    public function store(array $data)
    {
        return Cliente::create($this->transform($data));
    }

    public function update(Cliente $cliente, array $data)
    {
        $cliente->update($this->transform($data));
        
        return $cliente->fresh();
    }

    /**
     * Convierte camelCase a snake_case, solo si la llave viene en el array.
     */
    private function transform(array $data): array
    {
        $res = [];

        if (array_key_exists('nombreRazonSocial', $data)) $res['nombre_razon_social'] = $data['nombreRazonSocial'];
        if (array_key_exists('rifCi', $data)) $res['rif_ci'] = $data['rifCi'];
        if (array_key_exists('telefono', $data)) $res['telefono'] = $data['telefono'];
        if (array_key_exists('direccion', $data)) $res['direccion'] = $data['direccion'];
        if (array_key_exists('tipo', $data)) $res['tipo'] = $data['tipo'];
        if (array_key_exists('limiteCredito', $data)) $res['limite_credito'] = $data['limiteCredito'];
        if (array_key_exists('diasCredito', $data)) $res['dias_credito'] = $data['diasCredito'];
        if (array_key_exists('saldo', $data)) $res['saldo'] = $data['saldo'];

        return $res;
    }
}