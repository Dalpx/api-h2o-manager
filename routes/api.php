<?php

use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\DocumentoController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\ProveedorController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SucursalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    //Route::get('logout', [AuthController::class, 'logout']);
    //Route::apiResource('customers', CustomerController::class);
    Route::apiResource('cliente', ClienteController::class);

    Route::post('documentoFiscal/lote', [DocumentoController::class, 'bulkStore']);
    Route::apiResource('documentoFiscal', DocumentoController::class);

    Route::apiResource('usuario', UserController::class);

    Route::apiResource('sucursal', SucursalController::class);
    Route::apiResource('proveedor', ProveedorController::class);
    Route::apiResource('item', ItemController::class);
    //Route::post('invoices/bulk', ['uses' => 'InvoiceController@bulkStore']);
});
