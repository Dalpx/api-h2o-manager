<?php

namespace App\Services\V1;

use App\Models\Asiento;
use App\Models\AsientoDetalle;
use App\Models\CuentaContable;
use App\Models\DocumentoFiscal;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContabilidadAsientoService
{
    public const COD_CAJA = '1.1.01';

    public const COD_BANCO = '1.1.02';

    public const COD_CXC = '1.1.03';

    public const COD_INGRESO_DEFAULT = '4.1.01';

    /** Registra asiento contable manual (partida doble). */
    public function crearManual(array $data): Asiento
    {
        return DB::transaction(function () use ($data) {
            $detalles = $data['detalles'] ?? [];
            $this->validarPartidaDoble($detalles);

            $asiento = Asiento::create([
                'fecha' => Carbon::parse($data['fecha'] ?? now())->format('Y-m-d H:i:s'),
                'origen' => $data['origen'] ?? 'manual',
                'referencia' => $data['referencia'] ?? 'Asiento manual',
                'sucursal_id' => (int) ($data['sucursalId'] ?? 1),
            ]);

            foreach ($detalles as $linea) {
                AsientoDetalle::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => (int) $linea['cuentaId'],
                    'debe' => round((float) ($linea['debe'] ?? 0), 2),
                    'haber' => round((float) ($linea['haber'] ?? 0), 2),
                ]);
            }

            return $asiento->fresh(['detalles.cuenta']);
        });
    }

    /**
     * Asiento por factura de venta (contado o crédito).
     */
    public function registrarVenta(DocumentoFiscal $documento, array $data): ?Asiento
    {
        $total = round((float) ($data['total'] ?? $documento->total ?? 0), 2);
        if ($total <= 0) {
            return null;
        }

        $esCredito = app(CreditoClienteService::class)
            ->esVentaCredito((string) ($data['condicionesPago'] ?? $documento->condiciones_pago ?? ''));

        $cuentaCobroId = $esCredito
            ? $this->idPorCodigo(self::COD_CXC)
            : $this->idCuentaCobroPorMetodo((string) ($data['condicionesPago'] ?? ''));

        $lineasIngreso = $this->lineasIngresoPorDetalles($data['detalles'] ?? [], $total);

        $detalles = [];
        $detalles[] = [
            'cuentaId' => $cuentaCobroId,
            'debe' => $total,
            'haber' => 0,
        ];
        foreach ($lineasIngreso as $lin) {
            $detalles[] = [
                'cuentaId' => $lin['cuentaId'],
                'debe' => 0,
                'haber' => $lin['monto'],
            ];
        }

        return $this->crearManual([
            'fecha' => $data['fecha'] ?? $documento->fecha,
            'origen' => 'venta',
            'referencia' => $data['serieCorrelativo'] ?? $documento->serie_correlativo,
            'sucursalId' => $data['sucursalId'] ?? $documento->sucursal_id,
            'detalles' => $detalles,
        ]);
    }

    /**
     * Asiento por abono a cuenta por cobrar.
     */
    public function registrarAbono(
        int $sucursalId,
        float $monto,
        string $metodoPago,
        string $referencia
    ): ?Asiento {
        $monto = round($monto, 2);
        if ($monto <= 0) {
            return null;
        }

        return $this->crearManual([
            'fecha' => now()->format('Y-m-d H:i:s'),
            'origen' => 'cobro',
            'referencia' => $referencia,
            'sucursalId' => $sucursalId,
            'detalles' => [
                [
                    'cuentaId' => $this->idCuentaCobroPorMetodo($metodoPago),
                    'debe' => $monto,
                    'haber' => 0,
                ],
                [
                    'cuentaId' => $this->idPorCodigo(self::COD_CXC),
                    'debe' => 0,
                    'haber' => $monto,
                ],
            ],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $detalles
     */
    private function lineasIngresoPorDetalles(array $detalles, float $totalFactura): array
    {
        $porCuenta = [];

        foreach ($detalles as $d) {
            $itemId = (int) ($d['itemId'] ?? 0);
            $montoLinea = round((float) ($d['totalLineas'] ?? 0), 2);
            if ($montoLinea <= 0) {
                continue;
            }

            $cuentaId = self::COD_INGRESO_DEFAULT;
            if ($itemId > 0) {
                $item = Item::find($itemId);
                if ($item?->cuenta_contable_venta_id) {
                    $cuentaId = (int) $item->cuenta_contable_venta_id;
                } else {
                    $cuentaId = $this->idPorCodigo(self::COD_INGRESO_DEFAULT);
                }
            } else {
                $cuentaId = $this->idPorCodigo(self::COD_INGRESO_DEFAULT);
            }

            $porCuenta[$cuentaId] = ($porCuenta[$cuentaId] ?? 0) + $montoLinea;
        }

        if (empty($porCuenta)) {
            return [[
                'cuentaId' => $this->idPorCodigo(self::COD_INGRESO_DEFAULT),
                'monto' => $totalFactura,
            ]];
        }

        $suma = array_sum($porCuenta);
        if (abs($suma - $totalFactura) > 0.02 && $suma > 0) {
            $factor = $totalFactura / $suma;
            foreach ($porCuenta as $id => $m) {
                $porCuenta[$id] = round($m * $factor, 2);
            }
        }

        $lineas = [];
        foreach ($porCuenta as $cuentaId => $monto) {
            $lineas[] = ['cuentaId' => (int) $cuentaId, 'monto' => round($monto, 2)];
        }

        return $lineas;
    }

    private function idCuentaCobroPorMetodo(string $condicionesPago): int
    {
        $metodo = strtolower(explode('|', $condicionesPago)[0] ?? 'efectivo_usd');

        if (in_array($metodo, ['transferencia', 'pago_movil', 'punto'], true)) {
            return $this->idPorCodigo(self::COD_BANCO);
        }

        return $this->idPorCodigo(self::COD_CAJA);
    }

    private function idPorCodigo(string $codigo): int
    {
        $id = CuentaContable::query()->where('codigo', $codigo)->value('id');
        if (! $id) {
            throw ValidationException::withMessages([
                'cuenta' => ["Falta la cuenta contable {$codigo} en el plan."],
            ]);
        }

        return (int) $id;
    }

    /**
     * @param  array<int, array<string, mixed>>  $detalles
     */
    private function validarPartidaDoble(array $detalles): void
    {
        if (count($detalles) < 2) {
            throw ValidationException::withMessages([
                'detalles' => ['El asiento requiere al menos dos líneas.'],
            ]);
        }

        $debe = 0;
        $haber = 0;
        foreach ($detalles as $i => $linea) {
            $d = round((float) ($linea['debe'] ?? 0), 2);
            $h = round((float) ($linea['haber'] ?? 0), 2);
            if ($d < 0 || $h < 0 || ($d > 0 && $h > 0)) {
                throw ValidationException::withMessages([
                    "detalles.{$i}" => ['Cada línea debe tener solo debe o solo haber.'],
                ]);
            }
            if ($d <= 0 && $h <= 0) {
                throw ValidationException::withMessages([
                    "detalles.{$i}" => ['Monto en debe o haber requerido.'],
                ]);
            }
            $debe += $d;
            $haber += $h;
        }

        if (abs($debe - $haber) > 0.009) {
            throw ValidationException::withMessages([
                'detalles' => ['La suma del debe debe igualar la del haber.'],
            ]);
        }
    }
}
