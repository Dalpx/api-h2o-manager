<?php

namespace App\Services\V1;

use Illuminate\Support\Facades\DB;
use App\Models\DocumentoFiscal;
use Illuminate\Support\Carbon;

class DocumentoService
{
    public function __construct(
        protected InventarioService $inventarioService,
        protected CreditoClienteService $creditoClienteService,
        protected ContabilidadAsientoService $contabilidadAsientoService
    ) {}

    /**
     * Procesa un solo documento.
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sucursalId = (int) $data['sucursalId'];
            $referencia = $data['serieCorrelativo'] ?? 'Venta';
            $usuarioId = (int) ($data['usuarioId'] ?? 1);

            $this->inventarioService->registrarSalidaPorVenta(
                $sucursalId,
                $data['detalles'] ?? [],
                $referencia,
                $usuarioId
            );

            $mappedData = $this->transform($data, now());
            $documento = DocumentoFiscal::create($mappedData);

            $detalles = $this->transformDetalles($data['detalles'] ?? []);
            if (! empty($detalles)) {
                $documento->detalles()->createMany($detalles);
            }

            $this->creditoClienteService->registrarPorVenta($documento, $data);

            $this->contabilidadAsientoService->registrarVenta($documento, $data);

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

}
