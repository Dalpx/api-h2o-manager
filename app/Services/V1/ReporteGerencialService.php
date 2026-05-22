<?php

namespace App\Services\V1;

use App\Models\Cliente;
use App\Models\CuentaPorCobrar;
use App\Models\DocumentoFiscal;
use App\Models\Item;
use App\Models\MovimientoInventario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReporteGerencialService
{
    public function __construct(
        protected InventarioService $inventarioService,
        protected ContabilidadReporteService $contabilidadReportes
    ) {}

    /**
     * Métricas para el dashboard del gerente.
     */
    public function dashboard(int $sucursalId): array
    {
        $hoy = Carbon::today();

        $ventasHoy = DocumentoFiscal::query()
            ->where('sucursal_id', $sucursalId)
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'anulado');

        $cantidadVentasHoy = (int) (clone $ventasHoy)->count();
        $totalVentasHoyUsd = round((float) (clone $ventasHoy)->sum('total'), 2);

        $resumenInv = $this->inventarioService->resumen($sucursalId, 'PRODUCTO');
        $unidadesStock = round($resumenInv->sum(fn ($r) => (float) ($r['stock'] ?? 0)), 2);
        $alertasStockBajo = $resumenInv->filter(fn ($r) => ! empty($r['stockBajo']))->count();

        $clientes = Cliente::query()->whereNull('deleted_at')->get();
        $activos = 0;
        $morosos = 0;
        $cxcPendiente = 0.0;

        foreach ($clientes as $c) {
            $saldo = (float) ($c->saldo ?? 0);
            if ($saldo <= 0.009) {
                $activos++;
            } else {
                $morosos++;
            }
            $cxcPendiente += $saldo;
        }

        $cxcDocumentos = (float) CuentaPorCobrar::query()
            ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
            ->sum('saldo');

        $serviciosActivos = Item::query()
            ->whereNull('deleted_at')
            ->where('tipo', 'SERVICIO')
            ->count();

        return [
            'sucursalId' => $sucursalId,
            'fecha' => $hoy->format('Y-m-d'),
            'unidadesStock' => $unidadesStock,
            'alertasStockBajo' => $alertasStockBajo,
            'ventasHoy' => [
                'cantidad' => $cantidadVentasHoy,
                'totalUsd' => $totalVentasHoyUsd,
            ],
            'clientes' => [
                'activos' => $activos,
                'morosos' => $morosos,
                'total' => $clientes->count(),
            ],
            'cxcPendienteUsd' => round($cxcPendiente, 2),
            'cxcDocumentosUsd' => round($cxcDocumentos, 2),
            'serviciosActivos' => $serviciosActivos,
        ];
    }

    /**
     * Genera un informe gerencial según tipo.
     */
    public function generar(string $tipo, array $params): array
    {
        $sucursalId = isset($params['sucursalId']) ? (int) $params['sucursalId'] : null;
        $fechaDesde = $params['fechaDesde'] ?? null;
        $fechaHasta = $params['fechaHasta'] ?? Carbon::today()->format('Y-m-d');

        return match ($tipo) {
            'ventas' => $this->informeVentas($fechaDesde, $fechaHasta, $sucursalId),
            'inventario' => $this->informeInventario($sucursalId),
            'movimientos_inventario' => $this->informeMovimientosInventario($fechaDesde, $fechaHasta, $sucursalId),
            'clientes_cxc' => $this->informeClientesCxc(),
            'balance_general' => $this->informeContable('balance', $fechaDesde, $fechaHasta, $sucursalId),
            'estado_resultados' => $this->informeContable('resultados', $fechaDesde, $fechaHasta, $sucursalId),
            default => throw ValidationException::withMessages([
                'tipo' => ['Tipo de informe no válido.'],
            ]),
        };
    }

    private function informeVentas(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId): array
    {
        $query = DocumentoFiscal::query()
            ->with(['cliente', 'detalles.item'])
            ->where('estado', '!=', 'anulado');

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }
        if ($fechaDesde) {
            $query->where('fecha', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->where('fecha', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }

        $docs = $query->orderByDesc('fecha')->get();

        $filas = $docs->map(fn ($d) => [
            'id' => $d->id,
            'fecha' => $d->fecha,
            'serieCorrelativo' => $d->serie_correlativo,
            'cliente' => $d->cliente?->nombre_razon_social ?? '—',
            'tipoDoc' => $d->tipo_doc,
            'condicionesPago' => $d->condiciones_pago,
            'subtotal' => (float) $d->subtotal,
            'iva' => (float) $d->iva,
            'total' => (float) $d->total,
            'estado' => $d->estado,
            'lineas' => $d->detalles->count(),
        ]);

        return [
            'tipo' => 'ventas',
            'titulo' => 'Informe de ventas',
            'periodo' => ['desde' => $fechaDesde, 'hasta' => $fechaHasta],
            'totales' => [
                'documentos' => $filas->count(),
                'subtotal' => round($filas->sum('subtotal'), 2),
                'iva' => round($filas->sum('iva'), 2),
                'total' => round($filas->sum('total'), 2),
            ],
            'filas' => $filas->values()->all(),
            'columnas' => ['fecha', 'serieCorrelativo', 'cliente', 'condicionesPago', 'subtotal', 'iva', 'total', 'estado'],
        ];
    }

    private function informeInventario(?int $sucursalId): array
    {
        $sid = $sucursalId ?? 1;
        $items = $this->inventarioService->resumen($sid, null);

        $filas = $items->map(fn ($r) => [
            'sku' => $r['sku'],
            'nombre' => $r['nombre'],
            'tipo' => $r['tipo'],
            'stock' => $r['controlaStock'] ? (float) $r['stock'] : null,
            'stockMinimo' => (float) $r['stockMinimo'],
            'stockBajo' => (bool) $r['stockBajo'],
            'precioSugerido' => (float) $r['precioSugerido'],
            'valorEstimado' => $r['controlaStock']
                ? round((float) $r['stock'] * (float) $r['precioSugerido'], 2)
                : null,
        ]);

        return [
            'tipo' => 'inventario',
            'titulo' => 'Informe de inventario',
            'periodo' => null,
            'totales' => [
                'items' => $filas->count(),
                'unidadesStock' => round($filas->whereNotNull('stock')->sum('stock'), 2),
                'valorEstimado' => round($filas->sum(fn ($f) => (float) ($f['valorEstimado'] ?? 0)), 2),
                'alertasStockBajo' => $filas->where('stockBajo', true)->count(),
            ],
            'filas' => $filas->values()->all(),
            'columnas' => ['sku', 'nombre', 'tipo', 'stock', 'stockMinimo', 'stockBajo', 'precioSugerido', 'valorEstimado'],
        ];
    }

    private function informeMovimientosInventario(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId): array
    {
        $query = MovimientoInventario::query()
            ->with(['detalles.item', 'sucursal', 'usuario']);

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }
        if ($fechaDesde) {
            $query->where('fecha', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->where('fecha', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }

        $movs = $query->orderByDesc('fecha')->get();
        $filas = [];

        foreach ($movs as $m) {
            foreach ($m->detalles as $d) {
                $filas[] = [
                    'fecha' => $m->fecha,
                    'tipo' => $m->tipo,
                    'referenciaDoc' => $m->referencia_doc,
                    'item' => $d->item?->nombre ?? '—',
                    'cantidad' => (float) $d->cantidad,
                    'signo' => (int) $d->signo,
                    'motivo' => $d->motivo,
                    'usuario' => $m->usuario?->nombre ?? '—',
                ];
            }
        }

        $entradas = collect($filas)->where('signo', '>', 0)->sum('cantidad');
        $salidas = collect($filas)->where('signo', '<', 0)->sum('cantidad');

        return [
            'tipo' => 'movimientos_inventario',
            'titulo' => 'Informe de movimientos de inventario',
            'periodo' => ['desde' => $fechaDesde, 'hasta' => $fechaHasta],
            'totales' => [
                'movimientos' => $movs->count(),
                'lineas' => count($filas),
                'unidadesEntrada' => round($entradas, 2),
                'unidadesSalida' => round(abs($salidas), 2),
            ],
            'filas' => $filas,
            'columnas' => ['fecha', 'tipo', 'item', 'cantidad', 'signo', 'referenciaDoc', 'motivo', 'usuario'],
        ];
    }

    private function informeClientesCxc(): array
    {
        $clientes = Cliente::query()
            ->whereNull('deleted_at')
            ->orderByDesc('saldo')
            ->get();

        $filas = $clientes->map(function ($c) {
            $saldo = (float) ($c->saldo ?? 0);
            $limite = (float) ($c->limite_credito ?? 0);
            $estado = 'activo';
            if ($saldo > 0.009) {
                $estado = ($limite > 0 && $saldo > $limite) ? 'sobre_limite' : 'moroso';
            }

            return [
                'id' => $c->id,
                'nombre' => $c->nombre_razon_social,
                'documento' => $c->rif_ci,
                'saldo' => $saldo,
                'limiteCredito' => $limite,
                'diasCredito' => (int) ($c->dias_credito ?? 0),
                'estado' => $estado,
            ];
        });

        $cxcDocs = CuentaPorCobrar::query()
            ->with(['cliente', 'documento'])
            ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
            ->orderBy('vencimiento')
            ->get()
            ->map(fn ($cx) => [
                'cliente' => $cx->cliente?->nombre_razon_social ?? '—',
                'serieCorrelativo' => $cx->documento?->serie_correlativo,
                'fecha' => $cx->fecha,
                'vencimiento' => $cx->vencimiento,
                'saldo' => (float) $cx->saldo,
                'estado' => $cx->estado,
            ]);

        return [
            'tipo' => 'clientes_cxc',
            'titulo' => 'Informe de cartera y clientes',
            'periodo' => null,
            'totales' => [
                'clientes' => $filas->count(),
                'morosos' => $filas->whereIn('estado', ['moroso', 'sobre_limite'])->count(),
                'saldoTotal' => round($filas->sum('saldo'), 2),
                'cxcPendientes' => $cxcDocs->count(),
                'saldoCxcDocumentos' => round($cxcDocs->sum('saldo'), 2),
            ],
            'filas' => $filas->values()->all(),
            'cxcDocumentos' => $cxcDocs->values()->all(),
            'columnas' => ['nombre', 'documento', 'saldo', 'limiteCredito', 'diasCredito', 'estado'],
        ];
    }

    private function informeContable(string $modo, ?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId): array
    {
        if ($modo === 'balance') {
            $data = $this->contabilidadReportes->balanceGeneral($fechaDesde, $fechaHasta, $sucursalId);

            return [
                'tipo' => 'balance_general',
                'titulo' => 'Balance general',
                'periodo' => ['desde' => $fechaDesde, 'hasta' => $fechaHasta],
                'totales' => $data['totales'] ?? [],
                'secciones' => $data['secciones'] ?? [],
                'filas' => [],
                'columnas' => [],
            ];
        }

        $data = $this->contabilidadReportes->estadoResultados($fechaDesde, $fechaHasta, $sucursalId);

        return [
            'tipo' => 'estado_resultados',
            'titulo' => 'Estado de resultados',
            'periodo' => ['desde' => $fechaDesde, 'hasta' => $fechaHasta],
            'totales' => $data['totales'] ?? [],
            'ingresos' => $data['ingresos'] ?? [],
            'egresos' => $data['egresos'] ?? [],
            'nota' => $data['nota'] ?? null,
            'secciones' => [],
            'filas' => [],
            'columnas' => [],
        ];
    }
}
