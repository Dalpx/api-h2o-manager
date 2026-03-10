<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsientoDetalle extends Model
{
    protected $table = 'asiento_detalle';

    protected $fillable = [
        'asiento_id',
        'cuenta_id',
        'debe',
        'haber'
    ];

    /**
     * Relación con el encabezado del asiento.
     */
    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'asiento_id');
    }

    /**
     * Relación con la cuenta contable afectada.
     */
    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_id');
    }
}