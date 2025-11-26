<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rutina extends Model
{
    protected $table = 'rutinas';

    protected $fillable = [
        'socio_id',
        'instructor_id',
        'ejercicio',
        'series',
        'repeticiones',
        'creada_en',
    ];

    protected $casts = [
        'creada_en' => 'date',
    ];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(User::class, 'socio_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }
}
