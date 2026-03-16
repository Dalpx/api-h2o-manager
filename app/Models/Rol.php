<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    protected $table = 'rol';
    protected $fillable = [
        'nombre'
    ];

    public function usuarios(): HasMany // Nombre en plural para relaciones de "Muchos"
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
