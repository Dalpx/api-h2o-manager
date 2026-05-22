<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanContableSeeder extends Seeder
{
    public function run(): void
    {
        $cuentas = [
            ['codigo' => '1.1.01', 'nombre' => 'Caja General', 'tipo' => 'Activo'],
            ['codigo' => '1.1.02', 'nombre' => 'Bancos Nacionales', 'tipo' => 'Activo'],
            ['codigo' => '1.1.03', 'nombre' => 'Cuentas por Cobrar Clientes', 'tipo' => 'Activo'],
            ['codigo' => '1.1.04', 'nombre' => 'Inventario de Mercancía', 'tipo' => 'Activo'],
            ['codigo' => '2.1.01', 'nombre' => 'Cuentas por Pagar Proveedores', 'tipo' => 'Pasivo'],
            ['codigo' => '3.1.01', 'nombre' => 'Capital Social', 'tipo' => 'Patrimonio'],
            ['codigo' => '3.1.02', 'nombre' => 'Resultados Acumulados', 'tipo' => 'Patrimonio'],
            ['codigo' => '4.1.01', 'nombre' => 'Ventas de Recarga de Agua', 'tipo' => 'Ingreso'],
            ['codigo' => '4.1.02', 'nombre' => 'Ventas de Productos', 'tipo' => 'Ingreso'],
            ['codigo' => '5.1.01', 'nombre' => 'Costo de Ventas', 'tipo' => 'Egreso'],
        ];

        foreach ($cuentas as $c) {
            DB::table('cuenta_contable')->updateOrInsert(
                ['codigo' => $c['codigo']],
                array_merge($c, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
