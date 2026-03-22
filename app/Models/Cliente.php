<?php

// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use SoftDeletes; // Activa el borrado lógico

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
        return $this->hasMany(DocumentoFiscal::class, 'cliente_id');
    }
}