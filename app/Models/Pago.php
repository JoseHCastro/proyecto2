<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'usuario_id',
        'suscripcion_id',
        'motivo',
        'monto',
        'metodo',
        'estado',
        'fecha',
        'vence',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vence' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function suscripcion(): BelongsTo
    {
        return $this->belongsTo(Suscripcion::class);
    }
}
