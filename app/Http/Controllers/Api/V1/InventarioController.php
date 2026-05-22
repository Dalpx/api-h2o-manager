<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AjustarInventarioRequest;
use App\Http\Resources\V1\InventarioResumenResource;
use App\Http\Resources\V1\MovimientoInventarioResource;
use App\Services\V1\InventarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    public function resumen(Request $request)
    {
        $sucursalId = (int) $request->query('sucursalId', 1);
        $tipo = $request->query('tipo');

        $items = $this->inventarioService->resumen($sucursalId, $tipo);

        return InventarioResumenResource::collection($items);
    }

    public function catalogoVentas(Request $request)
    {
        $sucursalId = (int) $request->query('sucursalId', 1);
        $tipo = $request->query('tipo');

        $items = $this->inventarioService->catalogoVentas($sucursalId, $tipo);

        return InventarioResumenResource::collection($items);
    }

    public function ajustar(AjustarInventarioRequest $request): JsonResponse
    {
        $resultado = $this->inventarioService->ajustarStock($request->validated());

        return response()->json([
            'message' => 'Inventario actualizado correctamente.',
            'data' => [
                'itemId' => $resultado['itemId'],
                'stockAnterior' => $resultado['stockAnterior'],
                'stockNuevo' => $resultado['stockNuevo'],
                'cantidad' => $resultado['cantidad'],
                'direccion' => $resultado['direccion'],
                'movimiento' => new MovimientoInventarioResource($resultado['movimiento']),
            ],
        ]);
    }
}
