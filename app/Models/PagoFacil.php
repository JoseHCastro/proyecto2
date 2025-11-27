<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoFacil extends Model
{
    protected $fillable = [
        'user_id',
        'pedido_id',
        'pagofacil_transaction_id',
        'monto',
        'qr_image',
        'estado',
        'nombre_cliente',
        'ci_cliente',
        'telefono_cliente',
        'email_cliente',
        'fecha_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
