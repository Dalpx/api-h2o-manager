<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    protected $table = 'item';
    protected $fillable = [
        'sku',
        'nombre',
        'tipo',
        'unidad_medida',
        'grava_iva',
        'stock_minimo',
        'precio_sugerido',
        'proveedor_id',
        'cuenta_contable_venta_id'
    ];

    public function cuentaContableVenta()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_venta_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
