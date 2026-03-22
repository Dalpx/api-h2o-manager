<?php

// app/Http/Controllers/Api/V1/ClienteController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ClienteResource;
use App\Http\Resources\V1\ClienteCollection;
use App\Filters\V1\ClienteQuery;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreClienteRequest;
use App\Http\Requests\V1\UpdateClienteRequest;
use App\Services\V1\ClienteService;

class ClienteController extends Controller
{
    protected $service;

    public function __construct(ClienteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = new ClienteQuery();
        $filterItems = $filter->transform($request);
        $clients = Cliente::where($filterItems);
        $docFisc = $request->query('incluirDocFisc');

        if ($docFisc) {
            $clients = $clients->with('documentoFiscal.detalles');
        }

        return new ClienteCollection($clients->paginate()->appends($request->query()));
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = $this->service->store($request->validated());

        return new ClienteResource($cliente);
    }

    public function show(Cliente $cliente)
    {
        $incluirDoc = request()->query('incluirDocFisc');

        if ($incluirDoc) {
            $cliente->load('documentoFiscal.detalles');
        }

        return new ClienteResource($cliente);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        // Nota: Asegúrate de que la variable en la ruta api.php sea {cliente}
        $actualizado = $this->service->update($cliente, $request->validated());

        return new ClienteResource($actualizado);
    }

    public function destroy(Cliente $cliente)
    {
        // Como usamos SoftDeletes, esto no lo borra de la DB, 
        // solo le pone la fecha en 'deleted_at'
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ], 200); // Puedes usar 204 No Content si no quieres devolver un body
    }
}
