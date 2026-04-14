<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\V1\ItemService;
use App\Filters\V1\ItemQuery;
use App\Http\Requests\V1\StoreItemRequest;
use App\Http\Requests\V1\UpdateItemRequest;
use App\Http\Resources\V1\ItemResource;
use App\Http\Resources\V1\ItemCollection;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filter = new ItemQuery();
        $queryItems = $filter->transform($request);

        // Cargamos las relaciones de antemano (Eager Loading)
        $items = Item::with(['proveedor', 'cuentaContableVenta']);

        if ($queryItems) {
            $items->where($queryItems);
        }

        if ($request->query('incluirElim')) {
            $items->withTrashed();
        }

        // Retornamos la colección paginada
        return new ItemCollection($items->paginate()->appends($request->query()));
    }

    public function show(Item $item)
    {

        return new ItemResource($item);
    }


    public function store(StoreItemRequest $request)
    {
        $item = $this->service->store($request->validated());
        return new ItemResource($item->load(['proveedor', 'cuentaContableVenta']));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $itemActualizado = $this->service->update($item, $request->validated());
        return new ItemResource($itemActualizado->load(['proveedor', 'cuentaContableVenta']));
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['message' => 'Ítem eliminado (Soft Delete)'], 200);
    }
}
