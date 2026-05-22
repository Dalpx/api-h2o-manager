<?php

/**
 * Prueba rápida: venta a crédito actualiza saldo y CXC.
 * Uso: php scripts/test-venta-credito.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cliente;
use App\Models\CuentaPorCobrar;
use App\Models\Item;
use App\Models\InventarioExistencia;
use App\Services\V1\DocumentoService;
use Illuminate\Support\Facades\DB;

$cliente = Cliente::query()->where('limite_credito', '>', 0)->first();
if (! $cliente) {
    $cliente = Cliente::query()->first();
    if ($cliente) {
        $cliente->update(['limite_credito' => 500, 'dias_credito' => 15, 'saldo' => 0]);
    }
}
if (! $cliente) {
    fwrite(STDERR, "No hay clientes en BD.\n");
    exit(1);
}

$item = Item::query()->where('tipo', 'PRODUCTO')->first();
if (! $item) {
    fwrite(STDERR, "No hay productos.\n");
    exit(1);
}

$inv = InventarioExistencia::query()->where('item_id', $item->id)->where('sucursal_id', 1)->first();
if ($inv && (float) $inv->cantidad < 5) {
    $inv->update(['cantidad' => 50]);
}

$saldoAntes = (float) $cliente->saldo;
$cxcAntes = CuentaPorCobrar::where('cliente_id', $cliente->id)->count();

$service = app(DocumentoService::class);
$total = 25.50;

try {
    $doc = $service->store([
        'sucursalId' => 1,
        'usuarioId' => 1,
        'tipoDoc' => 'Factura',
        'serieCorrelativo' => 'TEST-CRED-'.time(),
        'fecha' => now()->format('Y-m-d H:i:s'),
        'clienteId' => $cliente->id,
        'condicionesPago' => 'credito|local',
        'subtotal' => $total,
        'iva' => 0,
        'total' => $total,
        'estado' => 'emitido',
        'detalles' => [
            [
                'itemId' => $item->id,
                'cantidad' => 1,
                'precioUnitario' => $total,
                'ivaMonto' => 0,
                'totalLineas' => $total,
            ],
        ],
    ]);
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: '.$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString()."\n");
    exit(1);
}

$cliente->refresh();
$cxcNueva = CuentaPorCobrar::where('doc_id', $doc->id)->first();

echo "Doc ID: {$doc->id}\n";
echo "Saldo antes: {$saldoAntes} -> después: {$cliente->saldo}\n";
echo "CXC creada: ".($cxcNueva ? "sí ({$cxcNueva->saldo}, {$cxcNueva->estado})" : 'no')."\n";

if ((float) $cliente->saldo !== $saldoAntes + $total || ! $cxcNueva) {
    exit(2);
}
echo "OK\n";
