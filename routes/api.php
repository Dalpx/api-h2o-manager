<?php

use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\DocumentoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function(){
    //Route::get('logout', [AuthController::class, 'logout']);
    //Route::apiResource('customers', CustomerController::class);
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('documentofiscal', DocumentoController::class);

    //Route::post('invoices/bulk', ['uses' => 'InvoiceController@bulkStore']);
});
