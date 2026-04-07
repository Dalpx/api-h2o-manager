<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Obtiene la sucursal donde se realizó el movimiento.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Obtiene el usuario que registró el movimiento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con los detalles del movimiento (los productos específicos).
     * Nota: Esta relación la usaremos cuando creemos la tabla de detalles.
     */
    public function detalles(): HasMany
    {
        // Asumiendo que la tabla de detalles se llamará 'movimiento_inventario_detalle'
        return $this->hasMany(MovimientoInventarioDetalle::class, 'movimiento_id');
    }
}