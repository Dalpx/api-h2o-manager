<?php

namespace App\Services\V1;

use App\Models\InventarioExistencia;
use Illuminate\Support\Facades\DB;

class InventarioExistenciaService
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    public function updateOrCreate(array $data): InventarioExistencia
    {
        $sucursalId = (int) $data['sucursalId'];
        $itemId = (int) $data['itemId'];
        $cantidad = (float) $data['cantidadActual'];

        $this->inventarioService->asegurarExistencia($sucursalId, $itemId, 0);

        DB::table('inventario_existencia')
            ->where('sucursal_id', $sucursalId)
            ->where('item_id', $itemId)
            ->update(['cantidad_actual' => $cantidad]);

        return InventarioExistencia::query()
            ->where('sucursal_id', $sucursalId)
            ->where('item_id', $itemId)
            ->firstOrFail();
    }

    public function transform(array $data): array
    {
        $res = [];
        $fields = [
            'sucursalId' => 'sucursal_id',
            'itemId' => 'item_id',
            'cantidadActual' => 'cantidad_actual',
        ];

        foreach ($fields as $jsonKey => $dbKey) {
            if (isset($data[$jsonKey])) {
                $res[$dbKey] = $data[$jsonKey];
            }
        }

        return $res;
    }
}
