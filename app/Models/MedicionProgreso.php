<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicionProgreso extends Model
{
    protected $table = 'mediciones_progreso';
    
    public $timestamps = false;

    protected $fillable = [
        'socio_id',
        'medido_en',
        'peso_kg',
        'estatura_cm',
        'grasa_corporal',
        'notas',
    ];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(User::class, 'socio_id');
    }
}
