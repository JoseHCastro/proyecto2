<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'dia_semana' => 'array',
    ];

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }
}
