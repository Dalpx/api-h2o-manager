<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentoFiscal extends Model
{
    protected $table = 'documento_fiscal';

    protected $casts = [
        'subtotal'  => 'float',    // Convierte a float con 2 decimales
        'iva'       => 'float',
    ];

    protected $fillable = [
        'sucursal_id',
        'tipo_doc',
        'serie_correlativo',
        'fecha',
        'cliente_id',
        'condiciones_pago',
        'subtotal',
        'iva',
        'total',
        'estado'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function detalles()
    {
        return $this->hasMany(DocumentoDetalle::class, 'doc_id');
    }
}
