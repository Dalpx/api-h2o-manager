<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\DocumentoController;
use App\Http\Controllers\Api\V1\InventarioController;
use App\Http\Controllers\Api\V1\InventarioExistenciaController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\MovimientoInventarioController;
use App\Http\Controllers\Api\V1\ProveedorController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SucursalController;
use App\Http\Controllers\Api\V1\TarifaRecargaController;
use App\Http\Controllers\Api\V1\ContabilidadController;
use App\Http\Controllers\Api\V1\ReporteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    //Route::apiResource('customers', CustomerController::class);
    Route::get('cliente/{cliente}/cuentas-por-cobrar', [ClienteController::class, 'cuentasPorCobrar']);
    Route::post('cliente/{cliente}/abono', [ClienteController::class, 'registrarAbono']);
    Route::apiResource('cliente', ClienteController::class);

    Route::prefix('contabilidad')->group(function () {
        Route::get('cuentas', [ContabilidadController::class, 'cuentas']);
        Route::post('cuentas', [ContabilidadController::class, 'storeCuenta']);
        Route::get('asientos', [ContabilidadController::class, 'asientos']);
        Route::post('asientos', [ContabilidadController::class, 'storeAsiento']);
        Route::get('asientos/{asiento}', [ContabilidadController::class, 'showAsiento']);
        Route::get('balance-general', [ContabilidadController::class, 'balanceGeneral']);
        Route::get('estado-resultados', [ContabilidadController::class, 'estadoResultados']);
        Route::get('resumen', [ContabilidadController::class, 'resumen']);
        Route::get('diagnostico', [ContabilidadController::class, 'diagnostico']);
    });

    Route::post('documentoFiscal/lote', [DocumentoController::class, 'bulkStore']);
    Route::apiResource('documentoFiscal', DocumentoController::class);

    Route::apiResource('usuario', UserController::class);
    Route::apiResource('sucursal', SucursalController::class);

    Route::apiResource('proveedor', ProveedorController::class);
    Route::apiResource('item', ItemController::class);
    Route::apiResource('tarifaRecarga', TarifaRecargaController::class);
    
    Route::get('reportes/dashboard', [ReporteController::class, 'dashboard']);
    Route::get('reportes/generar', [ReporteController::class, 'generar']);

    Route::get('inventario/resumen', [InventarioController::class, 'resumen']);
    Route::get('inventario/catalogo-ventas', [InventarioController::class, 'catalogoVentas']);
    Route::post('inventario/ajuste', [InventarioController::class, 'ajustar']);

    Route::apiResource('movimientoInventario', MovimientoInventarioController::class);
    Route::apiResource('inventarioExistencia', InventarioExistenciaController::class);
    //Route::post('invoices/bulk', ['uses' => 'InvoiceController@bulkStore']);
});
