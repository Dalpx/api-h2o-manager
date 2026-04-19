<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\DocumentoController;
use App\Http\Controllers\Api\V1\InventarioExistenciaController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\MovimientoInventarioController;
use App\Http\Controllers\Api\V1\ProveedorController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SucursalController;
use App\Http\Controllers\Api\V1\TarifaRecargaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Por si acaso el login está roto, aquí está el legacy

/*Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    //Route::get('logout', [AuthController::class, 'logout']);
    //Route::apiResource('customers', CustomerController::class);
    Route::apiResource('cliente', ClienteController::class);

    Route::post('documentoFiscal/lote', [DocumentoController::class, 'bulkStore']);
    Route::apiResource('documentoFiscal', DocumentoController::class);

    Route::apiResource('usuario', UserController::class);
    Route::apiResource('sucursal', SucursalController::class);

    Route::apiResource('proveedor', ProveedorController::class);
    Route::apiResource('item', ItemController::class);
    Route::apiResource('tarifaRecarga', TarifaRecargaController::class);

    Route::apiResource('movimientoInventario', MovimientoInventarioController::class);
    Route::apiResource('inventarioExistencia', InventarioExistenciaController::class);
    //Route::post('invoices/bulk', ['uses' => 'InvoiceController@bulkStore']);
});*/

Route::post('v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Cambiado: 'cajero,gerente,admin' (sin espacios)
    Route::middleware('ability:cajero,gerente,admin')->group(function () {
        Route::apiResource('v1/proveedor', ProveedorController::class);
        Route::apiResource('v1/item', ItemController::class);
        Route::apiResource('v1/tarifaRecarga', TarifaRecargaController::class);
        Route::post('v1/documentoFiscal/lote', [DocumentoController::class, 'bulkStore']);
        Route::apiResource('v1/documentoFiscal', DocumentoController::class);
        Route::apiResource('v1/cliente', ClienteController::class);
    });

    // Cambiado: 'admin,gerente' (sin espacios)
    Route::middleware('ability:admin,gerente')->group(function () {
        Route::apiResource('v1/sucursal', SucursalController::class);
    });

    Route::middleware('ability:admin')->group(function () {
        Route::apiResource('v1/movimientoInventario', MovimientoInventarioController::class);
        Route::apiResource('v1/inventarioExistencia', InventarioExistenciaController::class);
    });

    Route::post('v1/logout', [AuthController::class, 'logout']);
});