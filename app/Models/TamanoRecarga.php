<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TamanoRecarga extends Model
{
    use SoftDeletes;
    protected $table = 'tamano_recarga';
    protected $fillable = [
        'id',
        'nombre',
        'factor_consumo_agua',
        'created_at',
        'updated_at'
    ];

    public function tarifas()
    {
        return parent::hasMany(TarifaRecarga::class, 'tamano_id');
    }
}
