<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ClienteResource;
use App\Http\Resources\V1\ClienteCollection;
use App\Filters\V1\ClienteQuery;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // Obtenemos el parámetro de la URL (si existe)
        $incluirDoc = request()->query('incluirDocFisc');

        if ($incluirDoc) {
            // Cargamos las relaciones anidadas: Documentos -> Detalles
            $cliente->load('documentoFiscal.detalles');
        }

        return new ClienteResource($cliente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
