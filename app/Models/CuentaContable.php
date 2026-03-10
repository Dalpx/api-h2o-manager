<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaContable extends Model
{
    protected $table = 'cuenta_contable';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo'
    ];

    public function detallesAsiento(): HasMany
    {
        return $this->hasMany(AsientoDetalle::class, 'cuenta_id');
    }
}