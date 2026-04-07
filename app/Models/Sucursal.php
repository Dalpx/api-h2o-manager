<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursal';
    protected $fillable = [
        'nombre',
        'rif',
        'direccion',
        'correlativos_doc'
    ];
    protected $casts = ['correlativos_doc' => 'array']; // Para manejar el JSON

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'sucursal_id');
    }
}
