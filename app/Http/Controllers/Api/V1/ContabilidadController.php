<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAsientoRequest;
use App\Http\Requests\V1\StoreCuentaContableRequest;
use App\Http\Resources\V1\AsientoResource;
use App\Http\Resources\V1\CuentaContableResource;
use App\Models\Asiento;
use App\Models\CuentaContable;
use App\Services\V1\ContabilidadAsientoService;
use App\Services\V1\ContabilidadReporteService;
use Illuminate\Http\Request;

class ContabilidadController extends Controller
{
    public function __construct(
        protected ContabilidadReporteService $reportes,
        protected ContabilidadAsientoService $asientos
    ) {}

    public function cuentas()
    {
        return CuentaContableResource::collection($this->reportes->listarCuentas());
    }

    public function storeCuenta(StoreCuentaContableRequest $request)
    {
        $cuenta = CuentaContable::create($request->validated());

        return new CuentaContableResource($cuenta);
    }

    public function asientos(Request $request)
    {
        $query = Asiento::query()
            ->with('detalles.cuenta')
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('fechaDesde')) {
            $query->where('fecha', '>=', $request->query('fechaDesde'));
        }
        if ($request->filled('fechaHasta')) {
            $query->where('fecha', '<=', $request->query('fechaHasta').' 23:59:59');
        }
        if ($request->filled('sucursalId')) {
            $query->where('sucursal_id', (int) $request->query('sucursalId'));
        }
        if ($request->filled('origen')) {
            $query->where('origen', $request->query('origen'));
        }

        $paginated = $query->paginate((int) $request->query('perPage', 20));

        return AsientoResource::collection($paginated);
    }

    public function showAsiento(Asiento $asiento)
    {
        $asiento->load('detalles.cuenta');

        return new AsientoResource($asiento);
    }

    public function storeAsiento(StoreAsientoRequest $request)
    {
        $asiento = $this->asientos->crearManual($request->validated());

        return (new AsientoResource($asiento))
            ->response()
            ->setStatusCode(201);
    }

    public function balanceGeneral(Request $request)
    {
        $data = $this->reportes->balanceGeneral(
            $request->query('fechaDesde'),
            $request->query('fechaHasta'),
            $request->filled('sucursalId') ? (int) $request->query('sucursalId') : null
        );

        return response()->json(['data' => $data]);
    }

    public function estadoResultados(Request $request)
    {
        $data = $this->reportes->estadoResultados(
            $request->query('fechaDesde'),
            $request->query('fechaHasta'),
            $request->filled('sucursalId') ? (int) $request->query('sucursalId') : null
        );

        return response()->json(['data' => $data]);
    }

    public function resumen(Request $request)
    {
        $desde = $request->query('fechaDesde');
        $hasta = $request->query('fechaHasta');
        $sucursal = $request->filled('sucursalId') ? (int) $request->query('sucursalId') : null;

        return response()->json([
            'data' => [
                'balanceGeneral' => $this->reportes->balanceGeneral($desde, $hasta, $sucursal),
                'estadoResultados' => $this->reportes->estadoResultados($desde, $hasta, $sucursal),
                'diagnostico' => $this->reportes->diagnostico($desde, $hasta, $sucursal),
                'totalAsientos' => Asiento::query()
                    ->when($desde, fn ($q) => $q->where('fecha', '>=', $desde))
                    ->when($hasta, fn ($q) => $q->where('fecha', '<=', $hasta.' 23:59:59'))
                    ->when($sucursal, fn ($q) => $q->where('sucursal_id', $sucursal))
                    ->count(),
            ],
        ]);
    }

    public function diagnostico(Request $request)
    {
        return response()->json([
            'data' => $this->reportes->diagnostico(
                $request->query('fechaDesde'),
                $request->query('fechaHasta'),
                $request->filled('sucursalId') ? (int) $request->query('sucursalId') : null
            ),
        ]);
    }
}
