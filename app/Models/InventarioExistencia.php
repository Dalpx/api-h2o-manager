<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Llave compuesta (sucursal_id + item_id). No usar save()/create()/fresh() de Eloquent;
 * actualizar vía query builder o InventarioService::setCantidadExistencia.
 */
class InventarioExistencia extends Model
{
    protected $table = 'inventario_existencia';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'sucursal_id',
        'item_id',
        'cantidad_actual',
    ];

    protected function setKeysForSaveQuery($query): Builder
    {
        return $query
            ->where('sucursal_id', $this->getAttribute('sucursal_id'))
            ->where('item_id', $this->getAttribute('item_id'));
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
