<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuenta_por_cobrar';

    protected $fillable = [
        'cliente_id', 
        'doc_id', 
        'fecha', 
        'vencimiento', 
        'saldo', 
        'estado'
    ];

    public function cliente(): BelongsTo {
        return $this->belongsTo(Cliente::class);
    }

    public function documento(): BelongsTo {
        return $this->belongsTo(DocumentoFiscal::class, 'doc_id');
    }

    public function abonos(): HasMany {
        return $this->hasMany(AbonoCxc::class, 'cxc_id');
    }
}