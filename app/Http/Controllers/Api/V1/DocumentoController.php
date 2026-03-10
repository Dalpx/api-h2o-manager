<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentoResource;
use App\Http\Resources\V1\DocumentoCollection;
use App\Filters\V1\DocumentoQuery;
use App\Models\DocumentoFiscal;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new DocumentoQuery();
        $filterItems = $filter->Transform($request);
        $docs = DocumentoFiscal::where($filterItems);
        $detalle = $request->query('incluirDetalle');

        if ($detalle) {
            $clients = $docs->with('detalles');
        }

        return new DocumentoCollection($docs->paginate()->appends($request->query()));
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
    public function show(DocumentoFiscal $documento)
    {

        //Hay un error en el show el cual no permite seleccionar una unica entrada por id
        //todo retorna null lmao

        // Obtenemos el parámetro de la URL (si existe)
        $incluirDoc = request()->query('incluirDetalle');

        if ($incluirDoc) {
            $documento->load('detalles');
        }

        return new DocumentoResource($documento);
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
