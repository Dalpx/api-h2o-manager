<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    protected $table = 'item';
    protected $fillable = [
        'tipo',
        'nombre',
        'sku',
        'unidad_medida',
        'grava_iva',
        'proveedor_id',
        'cuenta_contable_venta_id',
    ];
}
