<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventarioExistencia;
use App\Services\V1\InventarioExistenciaService;
use App\Filters\V1\InventarioExistenciaQuery;
use App\Http\Requests\V1\StoreInventarioExistenciaRequest;
use App\Http\Resources\V1\InventarioExistenciaResource;
use App\Http\Resources\V1\InventarioExistenciaCollection;
use Illuminate\Http\Request;

class InventarioExistenciaController extends Controller
{
    protected $service;

    public function __construct(InventarioExistenciaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = new InventarioExistenciaQuery();
        $queryItems = $filter->transform($request);

        // Eager Loading para evitar N+1
        $existencias = InventarioExistencia::with(['sucursal', 'item']);

        if ($queryItems) {
            $existencias->where($queryItems);
        }

        return new InventarioExistenciaCollection($existencias->paginate()->appends($request->query()));
    }

    public function store(StoreInventarioExistenciaRequest $request)
    {
        // Usamos updateOrCreate para que si ya existe la combinación sucursal/item, solo actualice el stock
        $existencia = $this->service->updateOrCreate($request->validated());
        return new InventarioExistenciaResource($existencia->load(['sucursal', 'item']));
    }
}
