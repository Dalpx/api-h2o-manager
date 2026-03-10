<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    protected $table = 'asiento';
    protected $fillable = [
        'fecha',
        'origen',
        'referencia',
        'sucursal_id'
    ];
}
