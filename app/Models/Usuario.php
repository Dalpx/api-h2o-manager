<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Model
{
    protected $table = 'usuario';

    protected $fillable = [
        'nombre',
        'cedula',
        'password',
        'rol',
        'sucursal_id',
        'activo'
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Relación con la sucursal.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
