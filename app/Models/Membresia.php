<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membresia extends Model
{
    protected $table = 'membresias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_dias',
        'activo',
    ];

    public function paquetes(): HasMany
    {
        return $this->hasMany(Paquete::class);
    }
}
