<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SucursalResource;
use App\Http\Resources\V1\SucursalCollection;
use App\Filters\V1\SucursalQuery;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreSucursalRequest;
use App\Http\Requests\V1\UpdateSucursalRequest;
use App\Services\V1\SucursalService;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource con filtrado automático.
     */
    public function index(Request $request)
    {
        $filter = new SucursalQuery();
        $filterItems = $filter->transform($request); // Filtros automáticos

        // Iniciamos la consulta base
        $sucursales = Sucursal::where($filterItems);

        return new SucursalCollection($sucursales->paginate()->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSucursalRequest $request, SucursalService $service)
    {
        // El servicio mapea y crea el registro
        $sucursal = $service->store($request->validated());

        // Retornamos el resource CamelCase
        return new SucursalResource($sucursal);
    }

    /**
     * Display the specified resource con Route Model Binding.
     */
    public function show(Sucursal $sucursal)
    {
        // Laravel ya inyectó el objeto real gracias a Route Model Binding
        return new SucursalResource($sucursal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSucursalRequest $request, Sucursal $sucursal, SucursalService $service)
    {
        // Pasamos el modelo actual y los datos nuevos al servicio
        // El nombre de variable $sucursal debe coincidir con el singular del apiResource
        $actualizado = $service->update($sucursal, $request->validated());

        return new SucursalResource($actualizado);
    }

    /**
     * Remove the specified resource from storage (Borrado físico).
     */
    public function destroy(Sucursal $sucursal, SucursalService $service)
    {
        // Delegamos el borrado físico al servicio
        $service->destroy($sucursal);

        return response()->json([
            'message' => 'Sucursal eliminada físicamente de forma correcta'
        ], 200);
    }
}
