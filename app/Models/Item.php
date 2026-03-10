<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $fillable = [
        'tipo',
        'nombre',
        'sku',
        'unidad_medida',
        'grava_iva',
        'proveedor_id',
        'cuenta_contable_venta_id',
        'activo'
    ];
}
