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
        $filterItems = $filter->Transform($request); // Filtros automáticos

        // Iniciamos la consulta con el Join
        // Especificamos la tabla base para evitar ambigüedad en los selects
        $docs = DocumentoFiscal::join('sucursal', 'documento_fiscal.sucursal_id', '=', 'sucursal.id')
            ->select('documento_fiscal.*');

        //Filtro manual por nombre de sucursal
        if ($request->filled('sucursal')) {
            $valor = $request->query('sucursal');

            // Manejamos si el usuario envía ?sucursal[eq]=Valencia o solo ?sucursal=Valencia
            $nombre = is_array($valor) ? ($valor['eq'] ?? null) : $valor;

            if ($nombre) {
                $docs->where('sucursal.nombre', 'like', "%{$nombre}%");
            }
        }

        //Aplicamos los filtros de DocumentoQuery
        // Agregamos el prefijo de la tabla a cada filtro para evitar errores de SQL
        foreach ($filterItems as $item) {
            $docs->where("documento_fiscal." . $item[0], $item[1], $item[2]);
        }

        //Carga de detalles si se solicita
        if ($request->query('incluirDetalle')) {
            $docs->with('detalles');
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
    public function show(DocumentoFiscal $documentoFiscal, Request $request)
    {

        //Hay un error en el show el cual no permite seleccionar una unica entrada por id
        //todo retorna null lmao

        // Obtenemos el parámetro de la URL (si existe)
        $incluirDoc = $request->query('incluirDetalle');

        if ($incluirDoc) {
            $documentoFiscal->load('detalles');
        }

        return new DocumentoResource($documentoFiscal);
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
