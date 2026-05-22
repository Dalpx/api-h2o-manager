<?php

namespace App\Services\V1;

use App\Models\InventarioExistencia;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\MovimientoInventarioDetalle;
use App\Models\Sucursal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    /** Tipos que descuentan existencia al vender. */
    public const TIPOS_CON_STOCK = ['PRODUCTO', 'INSUMO'];

    public const TIPOS_CATALOGO_VENTA = ['PRODUCTO', 'SERVICIO', 'INSUMO'];

    /**
     * Resumen de inventario (productos, servicios e insumos) por sucursal.
     */
    public function resumen(int $sucursalId, ?string $tipo = null): Collection
    {
        return $this->listarItemsConExistencia($sucursalId, $tipo);
    }

    /**
     * Catálogo para el módulo de ventas (misma estructura que resumen).
     */
    public function catalogoVentas(int $sucursalId, ?string $tipo = null): Collection
    {
        return $this->listarItemsConExistencia($sucursalId, $tipo);
    }

    /**
     * Lista ítems del catálogo con stock y flag controlaStock.
     */
    private function listarItemsConExistencia(int $sucursalId, ?string $tipo = null): Collection
    {
        $query = Item::query()
            ->with(['proveedor', 'cuentaContableVenta'])
            ->whereNull('deleted_at')
            ->whereIn('tipo', self::TIPOS_CATALOGO_VENTA);

        if ($tipo && in_array(strtoupper($tipo), self::TIPOS_CATALOGO_VENTA, true)) {
            $query->where('tipo', strtoupper($tipo));
        }

        $items = $query
            ->orderByRaw("CASE tipo WHEN 'PRODUCTO' THEN 1 WHEN 'SERVICIO' THEN 2 WHEN 'INSUMO' THEN 3 ELSE 4 END")
            ->orderBy('nombre')
            ->get();

        $existencias = InventarioExistencia::query()
            ->where('sucursal_id', $sucursalId)
            ->whereIn('item_id', $items->pluck('id'))
            ->get()
            ->keyBy('item_id');

        return $items->map(function (Item $item) use ($existencias, $sucursalId) {
            $controlaStock = in_array($item->tipo, self::TIPOS_CON_STOCK, true);
            $stock = $controlaStock
                ? (float) ($existencias->get($item->id)?->cantidad_actual ?? 0)
                : null;
            $min = (float) ($item->stock_minimo ?? 0);

            return $this->mapItemConStock($item, $sucursalId, $stock ?? 0, $min, $controlaStock);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function mapItemConStock(
        Item $item,
        int $sucursalId,
        float $stock,
        float $min,
        ?bool $controlaStock = null
    ): array {
        $controlaStock ??= in_array($item->tipo, self::TIPOS_CON_STOCK, true);

        return [
            'id' => $item->id,
            'sku' => $item->sku,
            'nombre' => $item->nombre,
            'tipo' => $item->tipo,
            'unidadMedida' => $item->unidad_medida,
            'gravaIva' => (bool) $item->grava_iva,
            'stockMinimo' => $min,
            'precioSugerido' => (float) ($item->precio_sugerido ?? 0),
            'proveedorId' => $item->proveedor_id,
            'proveedorNombre' => $item->proveedor?->razon_social,
            'cuentaContableVentaId' => $item->cuenta_contable_venta_id,
            'sucursalId' => $sucursalId,
            'stock' => $stock,
            'stockBajo' => $controlaStock && $stock <= $min,
            'controlaStock' => $controlaStock,
        ];
    }

    /**
     * Ajuste manual de stock (entrada o salida) con movimiento y detalle en una transacción.
     */
    public function ajustarStock(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $sucursalId = (int) $data['sucursalId'];
            $itemId = (int) $data['itemId'];
            $cantidad = (float) $data['cantidad'];
            $direccion = $data['direccion'];
            $usuarioId = (int) $data['usuarioId'];

            if ($cantidad <= 0) {
                throw ValidationException::withMessages([
                    'cantidad' => ['La cantidad debe ser mayor a cero.'],
                ]);
            }

            $item = Item::findOrFail($itemId);
            if (! in_array($item->tipo, self::TIPOS_CON_STOCK, true)) {
                throw ValidationException::withMessages([
                    'itemId' => ['Los servicios no tienen stock físico; no se pueden registrar entradas ni salidas.'],
                ]);
            }

            $signo = $direccion === 'entrada' ? 1 : -1;
            $tipoMov = $direccion === 'entrada' ? 'compra' : 'ajuste';

            $existencia = InventarioExistencia::query()
                ->where('sucursal_id', $sucursalId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            if (! $existencia) {
                $existencia = $this->asegurarExistencia($sucursalId, $itemId, 0);
            }

            $stockAnterior = (float) $existencia->cantidad_actual;
            $stockNuevo = $stockAnterior + ($signo * $cantidad);

            if ($stockNuevo < 0) {
                throw ValidationException::withMessages([
                    'cantidad' => ["Stock insuficiente. Disponible: {$stockAnterior}."],
                ]);
            }

            $this->setCantidadExistencia($sucursalId, $itemId, $stockNuevo);
            $existencia->cantidad_actual = $stockNuevo;

            $referencia = $data['referenciaDoc']
                ?? ($data['motivo'] ?? 'Ajuste manual de inventario');

            $movimiento = MovimientoInventario::create([
                'fecha' => $data['fecha'] ?? Carbon::now()->format('Y-m-d H:i:s'),
                'sucursal_id' => $sucursalId,
                'tipo' => $tipoMov,
                'referencia_doc' => $referencia,
                'usuario_id' => $usuarioId,
            ]);

            MovimientoInventarioDetalle::create([
                'mov_id' => $movimiento->id,
                'item_id' => $itemId,
                'cantidad' => $cantidad,
                'costo_unitario' => (float) ($item->precio_sugerido ?? 0),
                'signo' => $signo,
                'motivo' => $data['motivo'] ?? null,
            ]);

            $movimiento->load(['sucursal', 'usuario', 'detalles.item']);

            return [
                'itemId' => $itemId,
                'stockAnterior' => $stockAnterior,
                'stockNuevo' => $stockNuevo,
                'cantidad' => $cantidad,
                'direccion' => $direccion,
                'movimiento' => $movimiento,
            ];
        });
    }

    /**
     * Registra salida de inventario por venta (documento fiscal) con auditoría.
     */
    public function registrarSalidaPorVenta(
        int $sucursalId,
        array $detalles,
        string $referenciaDoc,
        int $usuarioId = 1
    ): void {
        $cantidadPorItem = [];
        foreach ($detalles as $detalle) {
            $itemId = (int) ($detalle['itemId'] ?? 0);
            $cantidad = (float) ($detalle['cantidad'] ?? 0);
            if ($itemId <= 0 || $cantidad <= 0) {
                continue;
            }
            $cantidadPorItem[$itemId] = ($cantidadPorItem[$itemId] ?? 0) + $cantidad;
        }

        if (empty($cantidadPorItem)) {
            return;
        }

        DB::transaction(function () use ($sucursalId, $cantidadPorItem, $referenciaDoc, $usuarioId) {
            $movimiento = MovimientoInventario::create([
                'fecha' => Carbon::now()->format('Y-m-d H:i:s'),
                'sucursal_id' => $sucursalId,
                'tipo' => 'venta',
                'referencia_doc' => $referenciaDoc,
                'usuario_id' => $usuarioId,
            ]);

            foreach ($cantidadPorItem as $itemId => $cantidadVenta) {
                $item = Item::find($itemId);
                $controlaStock = $item && in_array($item->tipo, self::TIPOS_CON_STOCK, true);

                if ($controlaStock) {
                    $existencia = InventarioExistencia::query()
                        ->where('sucursal_id', $sucursalId)
                        ->where('item_id', $itemId)
                        ->lockForUpdate()
                        ->first();

                    if (! $existencia) {
                        throw ValidationException::withMessages([
                            'detalles' => ["No existe inventario para el item {$itemId} en la sucursal {$sucursalId}."],
                        ]);
                    }

                    $stockActual = (float) $existencia->cantidad_actual;
                    if ($stockActual < $cantidadVenta) {
                        throw ValidationException::withMessages([
                            'detalles' => ["Stock insuficiente para «{$item->nombre}». Disponible: {$stockActual}, solicitado: {$cantidadVenta}."],
                        ]);
                    }

                    $stockNuevo = $stockActual - $cantidadVenta;
                    $this->setCantidadExistencia($sucursalId, $itemId, $stockNuevo);
                    $existencia->cantidad_actual = $stockNuevo;
                }

                MovimientoInventarioDetalle::create([
                    'mov_id' => $movimiento->id,
                    'item_id' => $itemId,
                    'cantidad' => $cantidadVenta,
                    'costo_unitario' => (float) ($item?->precio_sugerido ?? 0),
                    'signo' => -1,
                    'motivo' => 'Venta — '.$referenciaDoc,
                ]);
            }
        });
    }

    /**
     * Actualiza cantidad sin usar save() (PK compuesta sucursal_id + item_id).
     */
    private function setCantidadExistencia(int $sucursalId, int $itemId, float $cantidad): void
    {
        InventarioExistencia::query()
            ->where('sucursal_id', $sucursalId)
            ->where('item_id', $itemId)
            ->update(['cantidad_actual' => $cantidad]);
    }

    /**
     * Crea filas de existencia en cero para todas las sucursales al registrar un ítem nuevo.
     */
    public function inicializarExistenciasParaItem(int $itemId): void
    {
        $sucursalIds = Sucursal::query()->pluck('id');

        foreach ($sucursalIds as $sucursalId) {
            $this->asegurarExistencia((int) $sucursalId, $itemId, 0);
        }
    }

    /**
     * Crea fila de existencia si no existe (evita Eloquent con PK compuesta).
     */
    public function asegurarExistencia(int $sucursalId, int $itemId, float $cantidadInicial = 0): InventarioExistencia
    {
        $existencia = InventarioExistencia::query()
            ->where('sucursal_id', $sucursalId)
            ->where('item_id', $itemId)
            ->first();

        if ($existencia) {
            return $existencia;
        }

        DB::table('inventario_existencia')->insert([
            'sucursal_id' => $sucursalId,
            'item_id' => $itemId,
            'cantidad_actual' => $cantidadInicial,
        ]);

        return InventarioExistencia::query()
            ->where('sucursal_id', $sucursalId)
            ->where('item_id', $itemId)
            ->firstOrFail();
    }
}
