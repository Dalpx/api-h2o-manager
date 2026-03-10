<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $fillable = [
        'razon_social',
        'rif',
        'contacto',
        'telefono',
        'direccion'
    ];
}
