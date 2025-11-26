<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informacion extends Model
{
    protected $table = 'informaciones';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
    ];
}
