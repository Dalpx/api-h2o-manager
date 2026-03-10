<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoDetalle extends Model
{
    protected $table = 'documento_detalle';

    protected $fillable = [
        'doc_id',
        'item_id',
        'tamano_id',
        'cantidad',
        'precio_unit',
        'iva_monto',
        'total_linea',
        'costo_estimado'
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(DocumentoFiscal::class, 'doc_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function tamano(): BelongsTo
    {
        return $this->belongsTo(TamanoRecarga::class, 'tamano_id');
    }
}