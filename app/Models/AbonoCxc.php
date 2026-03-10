<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbonoCxc extends Model
{
    protected $table = 'abono_cxc';

    protected $fillable = [
        'cxc_id', 
        'pago_id', 
        'monto'
    ];

    public function cuentaPorCobrar(): BelongsTo {
        return $this->belongsTo(CuentaPorCobrar::class, 'cxc_id');
    }

    public function pago(): BelongsTo {
        return $this->belongsTo(Pago::class);
    }
}