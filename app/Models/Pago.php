<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pago';

    protected $fillable = [
        'doc_id',
        'fecha',
        'metodo',
        'monto',
        'referencia_bancaria',
        'banco'
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(DocumentoFiscal::class, 'doc_id');
    }
}