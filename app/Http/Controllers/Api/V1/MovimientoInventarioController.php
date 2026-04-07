<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Services\V1\MovimientoInventarioService;
use App\Filters\V1\MovimientoInventarioQuery;
use App\Http\Requests\V1\StoreMovimientoInventarioRequest;
use App\Http\Requests\V1\UpdateMovimientoInventarioRequest;
use App\Http\Resources\V1\MovimientoInventarioResource;
use App\Http\Resources\V1\MovimientoInventarioCollection;
use Illuminate\Http\Request;

class MovimientoInventarioController extends Controller
{
    protected $service;

    public function __construct(MovimientoInventarioService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = new MovimientoInventarioQuery();
        $queryItems = $filter->transform($request);

        // Aplicamos Eager Loading para las relaciones
        $movimientos = MovimientoInventario::with(['sucursal', 'usuario']);

        if ($queryItems) {
            $movimientos->where($queryItems);
        }

        return new MovimientoInventarioCollection($movimientos->paginate()->appends($request->query()));
    }

    public function store(StoreMovimientoInventarioRequest $request)
    {
        $movimiento = $this->service->store($request->validated());
        return new MovimientoInventarioResource($movimiento->load(['sucursal', 'usuario']));
    }

    public function show(MovimientoInventario $movimientoInventario)
    {
        return new MovimientoInventarioResource($movimientoInventario->load(['sucursal', 'usuario']));
    }

    public function update(UpdateMovimientoInventarioRequest $request, MovimientoInventario $movimientoInventario)
    {
        $actualizado = $this->service->update($movimientoInventario, $request->validated());
        return new MovimientoInventarioResource($actualizado->load(['sucursal', 'usuario']));
    }

    public function destroy(MovimientoInventario $movimientoInventario)
    {
        //$movimientoInventario->delete();
        // Como no hay deleted_at, esto es un hard delete
        return response()->json(['message' => 'No estoy seguro si esto deberia poder eliminarse'], 200);
    }
}