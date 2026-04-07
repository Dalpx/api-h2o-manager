<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TarifaRecarga;
use App\Services\V1\TarifaRecargaService;
use App\Filters\V1\TarifaRecargaQuery;
use App\Http\Requests\V1\StoreTarifaRecargaRequest;
use App\Http\Requests\V1\UpdateTarifaRecargaRequest;
use App\Http\Resources\V1\TarifaRecargaResource;
use App\Http\Resources\V1\TarifaRecargaCollection;
use Illuminate\Http\Request;

class TarifaRecargaController extends Controller
{
    protected $service;

    public function __construct(TarifaRecargaService $service) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = new TarifaRecargaQuery();
        $queryItems = $filter->transform($request);

        // Eager Loading de tamaño, sucursal y usuario (creado_por)
        $tarifas = TarifaRecarga::with(['tamano', 'sucursal', 'usuario']);

        if ($queryItems) {
            $tarifas->where($queryItems);
        }

        return new TarifaRecargaCollection($tarifas->paginate()->appends($request->query()));
    }

    public function store(StoreTarifaRecargaRequest $request)
    {
        $tarifa = $this->service->store($request->validated());
        return new TarifaRecargaResource($tarifa->load(['tamano', 'sucursal', 'usuario']));
    }

    public function update(UpdateTarifaRecargaRequest $request, TarifaRecarga $tarifaRecarga)
    {
        $actualizada = $this->service->update($tarifaRecarga, $request->validated());
        return new TarifaRecargaResource($actualizada->load(['tamano', 'sucursal', 'usuario']));
    }

    public function show(TarifaRecarga $tarifaRecarga)
    {
        return new TarifaRecargaResource($tarifaRecarga->load(['tamano', 'sucursal', 'usuario']));
    }

    public function destroy(TarifaRecarga $tarifaRecarga)
    {
        $tarifaRecarga->delete();
        return response()->json(['message' => 'Tarifa eliminada'], 200);
    }
}