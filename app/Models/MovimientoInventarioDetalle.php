<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventarioDetalle extends Model
{
    protected $table = 'movimiento_inventario_detalle';

    protected $fillable = [
        'mov_id',
        'item_id',
        'cantidad',
        'costo_unitario',
        'signo',
        'motivo'
    ];

    public function movimiento(): BelongsTo
    {
        return $this->belongsTo(MovimientoInventario::class, 'mov_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
