<?php

namespace App\Services\V1;

use App\Models\CuentaContable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContabilidadReporteService
{
    /**
     * Movimientos de cuentas en un rango (para estado de resultados del período).
     */
    public function movimientosEnPeriodo(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId = null): Collection
    {
        return $this->queryMovimientos($fechaDesde, $fechaHasta, $sucursalId);
    }

    /**
     * Saldos acumulados hasta una fecha de corte (para balance general).
     */
    public function saldosAcumuladosHasta(?string $fechaCorte, ?int $sucursalId = null): Collection
    {
        return $this->queryMovimientos(null, $fechaCorte, $sucursalId);
    }

    private function queryMovimientos(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId): Collection
    {
        $query = DB::table('asiento_detalle as ad')
            ->join('asiento as a', 'a.id', '=', 'ad.asiento_id')
            ->join('cuenta_contable as c', 'c.id', '=', 'ad.cuenta_id')
            ->select(
                'c.id as cuentaId',
                'c.codigo',
                'c.nombre',
                'c.tipo',
                DB::raw('COALESCE(SUM(ad.debe), 0) as totalDebe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) as totalHaber')
            );

        if ($fechaDesde) {
            $query->where('a.fecha', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->where('a.fecha', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }
        if ($sucursalId) {
            $query->where('a.sucursal_id', $sucursalId);
        }

        return $query
            ->groupBy('c.id', 'c.codigo', 'c.nombre', 'c.tipo')
            ->orderBy('c.codigo')
            ->get();
    }

    /**
     * Balance general a una fecha de corte (posición acumulada).
     * Incluye resultado del ejercicio (ingresos − egresos acumulados) en patrimonio si no hay asiento de cierre.
     */
    public function balanceGeneral(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId = null): array
    {
        $corte = $fechaHasta ?: now()->format('Y-m-d');
        $movs = $this->saldosAcumuladosHasta($corte, $sucursalId);

        $tipos = ['Activo', 'Pasivo', 'Patrimonio'];
        $secciones = [];
        $totales = ['activo' => 0, 'pasivo' => 0, 'patrimonio' => 0, 'resultadoEjercicio' => 0];

        foreach ($tipos as $tipo) {
            $cuentas = $this->mapCuentasConSaldo($movs, $tipo);
            $subtotal = round($cuentas->sum('saldo'), 2);

            if ($tipo === 'Activo') {
                $totales['activo'] = $subtotal;
            } elseif ($tipo === 'Pasivo') {
                $totales['pasivo'] = $subtotal;
            } else {
                $totales['patrimonio'] = $subtotal;
            }

            $secciones[] = [
                'tipo' => $tipo,
                'cuentas' => $cuentas,
                'subtotal' => $subtotal,
            ];
        }

        $resultadoAcumulado = $this->utilidadAcumuladaHasta($corte, $sucursalId);
        $totales['resultadoEjercicio'] = $resultadoAcumulado;

        if (abs($resultadoAcumulado) > 0.009) {
            $idxPat = collect($secciones)->search(fn ($s) => $s['tipo'] === 'Patrimonio');
            if ($idxPat !== false) {
                $sec = $secciones[$idxPat];
                $sec['cuentas'] = collect($sec['cuentas'])->push([
                    'cuentaId' => null,
                    'codigo' => '—',
                    'nombre' => 'Resultado del ejercicio (ingresos − egresos, sin cerrar)',
                    'tipo' => 'Patrimonio',
                    'totalDebe' => $resultadoAcumulado < 0 ? abs($resultadoAcumulado) : 0,
                    'totalHaber' => $resultadoAcumulado > 0 ? $resultadoAcumulado : 0,
                    'saldo' => $resultadoAcumulado,
                    'esCalculado' => true,
                ])->values();
                $sec['subtotal'] = round(collect($sec['cuentas'])->sum('saldo'), 2);
                $secciones[$idxPat] = $sec;
                $totales['patrimonio'] = $sec['subtotal'];
            }
        }

        $totales['pasivoPatrimonio'] = round($totales['pasivo'] + $totales['patrimonio'], 2);
        $totales['diferencia'] = round($totales['activo'] - $totales['pasivoPatrimonio'], 2);
        $totales['cuadra'] = abs($totales['diferencia']) < 0.05;

        return [
            'fechaCorte' => $corte,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'sucursalId' => $sucursalId,
            'nota' => 'El balance usa saldos acumulados a la fecha de corte. El resultado del ejercicio se suma al patrimonio mientras no registres un asiento de cierre a Resultados acumulados.',
            'secciones' => $secciones,
            'totales' => $totales,
        ];
    }

    /**
     * Estado de resultados del período (solo ingresos y egresos con movimiento en el rango).
     */
    public function estadoResultados(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId = null): array
    {
        $desde = $fechaDesde ?: Carbon::now()->startOfYear()->format('Y-m-d');
        $hasta = $fechaHasta ?: now()->format('Y-m-d');

        $movs = $this->movimientosEnPeriodo($desde, $hasta, $sucursalId);

        $ingresos = $this->mapCuentasConSaldo($movs, 'Ingreso');
        $egresos = $this->mapCuentasConSaldo($movs, 'Egreso');

        $totalIngresos = round($ingresos->sum('saldo'), 2);
        $totalEgresos = round($egresos->sum('saldo'), 2);
        $utilidadNeta = round($totalIngresos - $totalEgresos, 2);

        return [
            'fechaDesde' => $desde,
            'fechaHasta' => $hasta,
            'sucursalId' => $sucursalId,
            'nota' => 'Muestra ingresos y egresos con movimiento entre las fechas indicadas.',
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'totales' => [
                'ingresos' => $totalIngresos,
                'egresos' => $totalEgresos,
                'utilidadNeta' => $utilidadNeta,
            ],
        ];
    }

    public function utilidadAcumuladaHasta(string $fechaCorte, ?int $sucursalId = null): float
    {
        $movs = $this->saldosAcumuladosHasta($fechaCorte, $sucursalId);
        $ingresos = $this->mapCuentasConSaldo($movs, 'Ingreso')->sum('saldo');
        $egresos = $this->mapCuentasConSaldo($movs, 'Egreso')->sum('saldo');

        return round($ingresos - $egresos, 2);
    }

    private function mapCuentasConSaldo(Collection $movs, string $tipo): Collection
    {
        return $movs->filter(fn ($r) => strcasecmp($r->tipo, $tipo) === 0)
            ->map(function ($r) use ($tipo) {
                $saldo = $this->saldoNaturaleza($tipo, (float) $r->totalDebe, (float) $r->totalHaber);

                return [
                    'cuentaId' => (int) $r->cuentaId,
                    'codigo' => $r->codigo,
                    'nombre' => $r->nombre,
                    'tipo' => $r->tipo,
                    'totalDebe' => (float) $r->totalDebe,
                    'totalHaber' => (float) $r->totalHaber,
                    'saldo' => round($saldo, 2),
                    'esCalculado' => false,
                ];
            })
            ->filter(fn ($c) => abs($c['saldo']) > 0.009)
            ->values();
    }

    private function saldoNaturaleza(string $tipo, float $debe, float $haber): float
    {
        $t = strtolower($tipo);

        if (in_array($t, ['activo', 'egreso'], true)) {
            return $debe - $haber;
        }

        return $haber - $debe;
    }

    public function listarCuentas(): Collection
    {
        return CuentaContable::query()->orderBy('codigo')->get();
    }

    /**
     * Diagnóstico: totales debe/haber de todos los asientos en un rango.
     */
    public function diagnostico(?string $fechaDesde, ?string $fechaHasta, ?int $sucursalId = null): array
    {
        $query = DB::table('asiento_detalle as ad')
            ->join('asiento as a', 'a.id', '=', 'ad.asiento_id');

        if ($fechaDesde) {
            $query->where('a.fecha', '>=', Carbon::parse($fechaDesde)->startOfDay());
        }
        if ($fechaHasta) {
            $query->where('a.fecha', '<=', Carbon::parse($fechaHasta)->endOfDay());
        }
        if ($sucursalId) {
            $query->where('a.sucursal_id', $sucursalId);
        }

        $row = $query->selectRaw('COALESCE(SUM(ad.debe),0) as debe, COALESCE(SUM(ad.haber),0) as haber')->first();

        return [
            'totalDebe' => round((float) ($row->debe ?? 0), 2),
            'totalHaber' => round((float) ($row->haber ?? 0), 2),
            'diferencia' => round((float) ($row->debe ?? 0) - (float) ($row->haber ?? 0), 2),
            'cuadra' => abs((float) $row->debe - (float) $row->haber) < 0.02,
        ];
    }
}
