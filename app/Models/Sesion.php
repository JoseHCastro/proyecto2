<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $fillable = [
        'disciplina_id',
        'horario_id',
        'instructor_id',
    ];

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function paquetes(): BelongsToMany
    {
        return $this->belongsToMany(Paquete::class, 'clase_paquete');
    }
}
