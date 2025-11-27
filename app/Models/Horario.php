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

    protected $appends = ['hora_inicio_formatted', 'hora_fin_formatted'];

    public function getHoraInicioFormattedAttribute()
    {
        return $this->hora_inicio ? substr($this->hora_inicio, 0, 5) : null;
    }

    public function getHoraFinFormattedAttribute()
    {
        return $this->hora_fin ? substr($this->hora_fin, 0, 5) : null;
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }
}
