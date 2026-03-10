<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{

    protected $table = 'cliente';
    protected $fillable = [
        'nombre_razon_social',
        'rif_ci',
        'telefono',
        'direccion',
        'tipo',
        'limite_credito',
        'dias_credito',
        'saldo'
    ];

    public function documentoFiscal(): HasMany
    {
        return $this->hasMany(DocumentoFiscal::class);
    }
}
