<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifaRecarga extends Model
{
    protected $table = 'tarifa_recarga';
    protected $fillable = [
        'tamano_id',
        'precio',
        'fecha_desde',
        'fecha_hasta',
        'sucursal_id',
        'creado_por',
        'audit_hash'
    ];

    public function tamano()
    {
        return parent::belongsTo(TamanoRecarga::class, 'tamano_id');
    }

    public function sucursal()
    {
        return parent::belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function usuario()
    {
        return parent::belongsTo(User::class, 'creado_por');
    }
}
