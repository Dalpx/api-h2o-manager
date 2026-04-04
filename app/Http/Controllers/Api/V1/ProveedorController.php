<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Filters\V1\ProveedorQuery;
use App\Http\Requests\V1\StoreProveedorRequest;
use App\Http\Requests\V1\UpdateProveedorRequest;
use App\Http\Resources\V1\ProveedorResource;
use App\Http\Resources\V1\ProveedorCollection;
use App\Services\V1\ProveedorService;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $filter = new ProveedorQuery();
        $queryItems = $filter->transform($request);

        $proveedores = Proveedor::query();

        // Aplicamos los filtros procesados por ProveedorQuery
        foreach ($queryItems as $item) {
            if ($item[1] === 'like') {
                $proveedores->where($item[0], 'like', '%' . $item[2] . '%');
            } else {
                $proveedores->where($item[0], $item[1], $item[2]);
            }
        }

        // Soporte para ver eliminados (Soft Deletes)
        if ($request->query('incluirElim')) {
            $proveedores->withTrashed();
        }

        return new ProveedorCollection($proveedores->paginate()->appends($request->query()));
    }

    public function store(StoreProveedorRequest $request, ProveedorService $service)
    {
        // El servicio se encarga del mapeo y la creación[cite: 29]
        $proveedor = $service->store($request->validated());
        return new ProveedorResource($proveedor);
    }

    public function show(Request $request, Proveedor $proveedor)
    {
        return new ProveedorResource($proveedor);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor, ProveedorService $service)
    {
        $proveedorActualizado = $service->update($proveedor, $request->validated());
        return new ProveedorResource($proveedorActualizado);
    }

    public function destroy(Proveedor $proveedor)
    {
        // El trait SoftDeletes en el modelo se encarga de que esto sea un borrado lógico
        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado con éxito'
        ], 200);
    }
}