<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function tamano($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        return parent::belongsTo(TamanoRecarga::class, 'tamano_id');
    }
}
