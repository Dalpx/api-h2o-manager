<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimiento_inventario';
    protected $fillable = [
        'fecha',
        'sucursal_id',
        'tipo',
        'referencia_doc',
        'usuario_id'
    ];
}
