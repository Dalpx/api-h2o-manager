<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioExistencia extends Model
{
    protected $table = 'inventario_existencia';
    public $timestamps = false;
    public $incrementing = false; // Importante para llaves compuestas
    protected $primaryKey = [
        'sucursal_id',
        'item_id'
    ]; // Laravel no soporta PK compuestas nativamente para Eloquent sin plugins, pero se define así para claridad.
    protected $fillable = [
        'sucursal_id',
        'item_id',
        'cantidad_actual'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
