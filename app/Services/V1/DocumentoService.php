<?php

namespace App\Services\V1;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentoFiscal;
use Illuminate\Support\Carbon;
use PDOException;

class DocumentoService
{
    /**
     * Procesa un solo documento.
     */
    public function store(array $data)
    {
        $mappedData = $this->transform($data, now());

        // Retornamos el modelo creado para que el controlador pueda usarlo en un Resource
        return DocumentoFiscal::create($mappedData);
    }

    /**
     * Procesa un lote de documentos.
     */
    public function storeBulk(array $docs)
    {
        $now = Carbon::now();

        $mappedDocs = array_map(function ($doc) use ($now) {
            return $this->transform($doc, $now);
        }, $docs);

        // Usamos el Query Builder para insertar el lote en una sola transacción SQL
        return DB::table('documento_fiscal')->insert($mappedDocs);
    }

    public function update(DocumentoFiscal $documento, array $data)
    {

        $mappedData = $this->transform($data);
        unset($mappedData['created_at'], $mappedData['updated_at']);

        $documento->update($mappedData);

        return $documento->fresh();
    }

    /**
     * Método privado para centralizar la traducción de CamelCase a SnakeCase.
     */
    private function transform(array $data, ?Carbon $timestamp = null): array
    {
        // 1. Definimos los datos básicos
        $res = [
            'sucursal_id'       => $data['sucursalId'],
            'tipo_doc'          => $data['tipoDoc'],
            'serie_correlativo' => $data['serieCorrelativo'],
            'fecha'             => $data['fecha'],
            'cliente_id'        => $data['clienteId'],
            'condiciones_pago'  => $data['condicionesPago'],
            'subtotal'          => $data['subtotal'],
            'iva'               => $data['iva'],
            'total'             => $data['total'],
            'estado'            => $data['estado'],
        ];

        // 2. SOLO si hay timestamp, agregamos las columnas de auditoría
        // Esto evita que Laravel intente formatear un 'null'
        if ($timestamp !== null) {
            $res['created_at'] = $timestamp;
            $res['updated_at'] = $timestamp;
        }

        return $res;
    }
}
