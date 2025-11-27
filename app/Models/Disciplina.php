<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disciplina extends Model
{
    protected $table = 'disciplinas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class);
    }
}
