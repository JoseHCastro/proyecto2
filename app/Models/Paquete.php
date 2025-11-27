<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paquete extends Model
{
    protected $table = 'paquetes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'precio',
        'membresia_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function membresia(): BelongsTo
    {
        return $this->belongsTo(Membresia::class);
    }

    public function sesiones(): BelongsToMany
    {
        return $this->belongsToMany(Sesion::class, 'clase_paquete');
    }

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class);
    }
}
