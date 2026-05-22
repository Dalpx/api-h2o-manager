<?php

namespace Database\Seeders;

use App\Models\InventarioExistencia;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\MovimientoInventarioDetalle;
use App\Models\Sucursal;
use App\Services\V1\InventarioService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoVentaSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        MovimientoInventarioDetalle::query()->delete();
        MovimientoInventario::query()->delete();
        DB::table('documento_detalle')->delete();
        DB::table('documento_fiscal')->delete();
        InventarioExistencia::query()->delete();
        Item::withTrashed()->forceDelete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $proveedorId = (int) DB::table('proveedor')->min('id') ?: 1;
        $cuentaId = (int) DB::table('cuenta_contable')->min('id') ?: 1;

        $catalogo = [
            [
                'tipo' => 'PRODUCTO',
                'nombre' => 'Botellón PET 20 Litros',
                'sku' => 'PRO-BOT-20L',
                'unidad_medida' => 'Unidad',
                'grava_iva' => true,
                'stock_minimo' => 10,
                'precio_sugerido' => 8.00,
                'stock_inicial' => 50,
            ],
            [
                'tipo' => 'SERVICIO',
                'nombre' => 'Recarga de Agua Purificada 20L',
                'sku' => 'SRV-REC-20L',
                'unidad_medida' => 'Servicio',
                'grava_iva' => false,
                'stock_minimo' => 0,
                'precio_sugerido' => 2.50,
                'stock_inicial' => null,
            ],
            [
                'tipo' => 'INSUMO',
                'nombre' => 'Tapa Plástica 55mm',
                'sku' => 'INS-TAP-55',
                'unidad_medida' => 'Unidad',
                'grava_iva' => true,
                'stock_minimo' => 50,
                'precio_sugerido' => 0.15,
                'stock_inicial' => 500,
            ],
        ];

        $inventarioService = app(InventarioService::class);
        $sucursalIds = Sucursal::query()->pluck('id');

        foreach ($catalogo as $data) {
            $item = Item::create([
                'tipo' => $data['tipo'],
                'nombre' => $data['nombre'],
                'sku' => $data['sku'],
                'unidad_medida' => $data['unidad_medida'],
                'grava_iva' => $data['grava_iva'],
                'stock_minimo' => $data['stock_minimo'],
                'precio_sugerido' => $data['precio_sugerido'],
                'proveedor_id' => $proveedorId,
                'cuenta_contable_venta_id' => $cuentaId,
            ]);

            $inventarioService->inicializarExistenciasParaItem($item->id);

            if ($data['stock_inicial'] !== null) {
                foreach ($sucursalIds as $sucursalId) {
                    InventarioExistencia::query()
                        ->where('sucursal_id', $sucursalId)
                        ->where('item_id', $item->id)
                        ->update(['cantidad_actual' => $data['stock_inicial']]);
                }
            }
        }

        $this->command?->info('Catálogo de venta: 1 producto, 1 servicio y 1 insumo creados.');
    }
}
