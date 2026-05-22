<?php

namespace App\Services\V1;

use App\Models\Cliente;
use App\Models\CuentaPorCobrar;
use App\Models\DocumentoFiscal;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CreditoClienteService
{
    public function esVentaCredito(string $condicionesPago): bool
    {
        return str_starts_with(strtolower(trim($condicionesPago)), 'credito');
    }

    /**
     * Registra deuda del cliente y cuenta por cobrar tras una factura a crédito.
     */
    public function registrarPorVenta(DocumentoFiscal $documento, array $data): void
    {
        if (! $this->esVentaCredito((string) ($data['condicionesPago'] ?? ''))) {
            return;
        }

        $cliente = Cliente::query()->lockForUpdate()->findOrFail((int) $data['clienteId']);
        $total = (float) ($data['total'] ?? 0);

        if ($total <= 0) {
            return;
        }

        $limite = (float) $cliente->limite_credito;
        $saldoActual = (float) $cliente->saldo;
        $nuevoSaldo = $saldoActual + $total;

        if ($limite <= 0) {
            throw ValidationException::withMessages([
                'condicionesPago' => ['El cliente no tiene límite de crédito configurado.'],
            ]);
        }

        if ($nuevoSaldo > $limite + 0.001) {
            $disponible = max(0, $limite - $saldoActual);
            throw ValidationException::withMessages([
                'condicionesPago' => [
                    "Crédito insuficiente. Disponible: ".number_format($disponible, 2).' · Límite: '.number_format($limite, 2),
                ],
            ]);
        }

        $dias = (int) $cliente->dias_credito;
        if ($dias <= 0) {
            $dias = 30;
        }

        $fecha = Carbon::parse($data['fecha'] ?? now());
        $vencimiento = $fecha->copy()->addDays($dias);

        $cliente->update(['saldo' => $nuevoSaldo]);

        CuentaPorCobrar::create([
            'cliente_id' => $cliente->id,
            'doc_id' => $documento->id,
            'fecha' => $fecha->format('Y-m-d H:i:s'),
            'vencimiento' => $vencimiento->format('Y-m-d H:i:s'),
            'saldo' => $total,
            'estado' => $vencimiento->isPast() ? 'VENCIDA' : 'PENDIENTE',
        ]);
    }
}
