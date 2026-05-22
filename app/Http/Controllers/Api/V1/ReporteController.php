<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\V1\ReporteGerencialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        protected ReporteGerencialService $reportes
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $sucursalId = (int) $request->query('sucursalId', 1);

        return response()->json([
            'data' => $this->reportes->dashboard($sucursalId),
        ]);
    }

    public function generar(Request $request): JsonResponse
    {
        $tipo = (string) $request->query('tipo', '');
        $params = [
            'sucursalId' => $request->filled('sucursalId') ? (int) $request->query('sucursalId') : null,
            'fechaDesde' => $request->query('fechaDesde'),
            'fechaHasta' => $request->query('fechaHasta'),
        ];

        $informe = $this->reportes->generar($tipo, $params);

        return response()->json(['data' => $informe]);
    }
}
