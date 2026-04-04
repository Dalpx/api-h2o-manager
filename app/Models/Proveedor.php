<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{

    use SoftDeletes;
    protected $table = 'proveedor';
    protected $fillable = [
        'razon_social',
        'rif',
        'contacto',
        'telefono',
        'direccion'
    ];
}
