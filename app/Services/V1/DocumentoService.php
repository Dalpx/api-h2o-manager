<?php

namespace App\Services\V1;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentoFiscal;
use App\Models\InventarioExistencia;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class DocumentoService
{
    /**
     * Procesa un solo documento.
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Primero validamos y descontamos inventario para evitar vender sin stock.
            $this->descontarInventario((int) $data['sucursalId'], $data['detalles'] ?? []);

            $mappedData = $this->transform($data, now());
            $documento = DocumentoFiscal::create($mappedData);

            $detalles = $this->transformDetalles($data['detalles'] ?? []);
            if (!empty($detalles)) {
                $documento->detalles()->createMany($detalles);
            }

            return $documento->fresh(['detalles.item']);
        });
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

    /**
     * Traduce detalles desde JSON camelCase al esquema DB.
     */
    private function transformDetalles(array $detalles): array
    {
        return array_map(function ($d) {
            return [
                'item_id' => $d['itemId'],
                'cantidad' => $d['cantidad'],
                'precio_unit' => $d['precioUnitario'],
                'iva_monto' => $d['ivaMonto'] ?? 0,
                'total_linea' => $d['totalLineas'],
                'tamano_id' => $d['tamanoId'] ?? null,
                'costo_estimado' => $d['costoEstimado'] ?? null,
            ];
        }, $detalles);
    }

    /**
     * Descuenta inventario por item en la sucursal del documento.
     * Usa lockForUpdate para asegurar consistencia en concurrencia.
     */
    private function descontarInventario(int $sucursalId, array $detalles): void
    {
        $cantidadPorItem = [];
        foreach ($detalles as $detalle) {
            $itemId = (int) ($detalle['itemId'] ?? 0);
            $cantidad = (float) ($detalle['cantidad'] ?? 0);
            if ($itemId <= 0 || $cantidad <= 0) {
                continue;
            }
            $cantidadPorItem[$itemId] = ($cantidadPorItem[$itemId] ?? 0) + $cantidad;
        }

        foreach ($cantidadPorItem as $itemId => $cantidadVenta) {
            $existencia = InventarioExistencia::where('sucursal_id', $sucursalId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$existencia) {
                throw ValidationException::withMessages([
                    'detalles' => ["No existe inventario para el item {$itemId} en la sucursal {$sucursalId}."],
                ]);
            }

            $stockActual = (float) $existencia->cantidad_actual;
            if ($stockActual < $cantidadVenta) {
                throw ValidationException::withMessages([
                    'detalles' => ["Stock insuficiente para item {$itemId}. Disponible: {$stockActual}, solicitado: {$cantidadVenta}."],
                ]);
            }

            $nuevoStock = $stockActual - $cantidadVenta;
            DB::table('inventario_existencia')
                ->where('sucursal_id', $sucursalId)
                ->where('item_id', $itemId)
                ->update(['cantidad_actual' => $nuevoStock]);
        }
    }
}
