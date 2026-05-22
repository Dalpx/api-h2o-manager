<?php

namespace App\Services\V1;

use App\Models\AbonoCxc;
use App\Models\Cliente;
use App\Models\CuentaPorCobrar;
use App\Models\Pago;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AbonoClienteService
{
    public function __construct(
        protected ContabilidadAsientoService $contabilidadAsientoService
    ) {}
    /**
     * Cuentas por cobrar con saldo pendiente del cliente.
     */
    public function listarPendientes(Cliente $cliente)
    {
        return CuentaPorCobrar::query()
            ->where('cliente_id', $cliente->id)
            ->where('saldo', '>', 0)
            ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
            ->with(['documento:id,serie_correlativo,total,fecha'])
            ->orderBy('vencimiento')
            ->orderBy('id')
            ->get();
    }

    /**
     * Registra un abono: reduce CXC, crea pago y abono_cxc, baja saldo del cliente.
     */
    public function registrar(Cliente $cliente, array $data): array
    {
        return DB::transaction(function () use ($cliente, $data) {
            $cliente = Cliente::query()->lockForUpdate()->findOrFail($cliente->id);
            $monto = round((float) ($data['monto'] ?? 0), 2);

            if ($monto <= 0) {
                throw ValidationException::withMessages([
                    'monto' => ['El monto del abono debe ser mayor a cero.'],
                ]);
            }

            $saldoCliente = (float) $cliente->saldo;
            if ($saldoCliente < $monto - 0.001) {
                throw ValidationException::withMessages([
                    'monto' => ['El abono no puede superar el saldo pendiente del cliente ($'.number_format($saldoCliente, 2).').'],
                ]);
            }

            $cxcId = isset($data['cxcId']) ? (int) $data['cxcId'] : null;
            $query = CuentaPorCobrar::query()
                ->where('cliente_id', $cliente->id)
                ->where('saldo', '>', 0)
                ->whereIn('estado', ['PENDIENTE', 'VENCIDA'])
                ->orderBy('vencimiento')
                ->orderBy('id');

            if ($cxcId) {
                $query->where('id', $cxcId);
            }

            $cuentas = $query->lockForUpdate()->get();

            if ($cuentas->isEmpty()) {
                throw ValidationException::withMessages([
                    'cxcId' => ['No hay cuentas por cobrar pendientes para este cliente.'],
                ]);
            }

            $totalEnCxc = $cuentas->sum(fn ($c) => (float) $c->saldo);
            if ($monto > $totalEnCxc + 0.001) {
                throw ValidationException::withMessages([
                    'monto' => ['El monto supera el saldo pendiente en facturas ($'.number_format($totalEnCxc, 2).').'],
                ]);
            }

            $metodo = $this->normalizarMetodo((string) ($data['metodo'] ?? 'efectivo_usd'));
            $fecha = isset($data['fecha'])
                ? Carbon::parse($data['fecha'])->format('Y-m-d H:i:s')
                : Carbon::now()->format('Y-m-d H:i:s');

            $restante = $monto;
            $detalleAbonos = [];

            foreach ($cuentas as $cxc) {
                if ($restante <= 0.001) {
                    break;
                }

                $saldoCxc = (float) $cxc->saldo;
                $aplicar = min($restante, $saldoCxc);

                $pago = Pago::create([
                    'doc_id' => $cxc->doc_id,
                    'fecha' => $fecha,
                    'metodo' => $metodo,
                    'monto' => $aplicar,
                    'referencia_bancaria' => $data['referenciaBancaria'] ?? null,
                    'banco' => $data['banco'] ?? null,
                ]);

                $abono = AbonoCxc::create([
                    'cxc_id' => $cxc->id,
                    'pago_id' => $pago->id,
                    'monto' => $aplicar,
                ]);

                $nuevoSaldoCxc = $saldoCxc - $aplicar;
                $cxc->update([
                    'saldo' => $nuevoSaldoCxc,
                    'estado' => $this->estadoCxc($cxc, $nuevoSaldoCxc),
                ]);

                $detalleAbonos[] = [
                    'abonoId' => $abono->id,
                    'cxcId' => $cxc->id,
                    'pagoId' => $pago->id,
                    'monto' => $aplicar,
                    'saldoRestanteCxc' => $nuevoSaldoCxc,
                ];

                $restante -= $aplicar;
            }

            $cliente->update([
                'saldo' => max(0, round($saldoCliente - $monto, 2)),
            ]);

            $this->contabilidadAsientoService->registrarAbono(
                (int) ($data['sucursalId'] ?? 1),
                $monto,
                (string) ($data['metodo'] ?? 'efectivo_usd'),
                'Abono cliente #'.$cliente->id
            );

            return [
                'clienteId' => $cliente->id,
                'montoAbonado' => $monto,
                'saldoCliente' => (float) $cliente->fresh()->saldo,
                'detalle' => $detalleAbonos,
            ];
        });
    }

    private function normalizarMetodo(string $metodo): string
    {
        $map = [
            'efectivo_usd' => 'Efectivo_USD',
            'efectivo_ves' => 'Efectivo_VES',
            'pago_movil' => 'PagoMovil',
            'transferencia' => 'Transferencia',
            'punto' => 'Punto',
            'credito' => 'Credito',
        ];

        $key = strtolower(str_replace(' ', '_', $metodo));

        return $map[$key] ?? $metodo;
    }

    private function estadoCxc(CuentaPorCobrar $cxc, float $nuevoSaldo): string
    {
        if ($nuevoSaldo <= 0.009) {
            return 'PAGADA';
        }

        $vence = Carbon::parse($cxc->vencimiento);

        return $vence->isPast() ? 'VENCIDA' : 'PENDIENTE';
    }
}
