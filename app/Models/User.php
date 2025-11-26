<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, \Spatie\Permission\Traits\HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'ci',
        'telefono',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function mediciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MedicionProgreso::class, 'socio_id');
    }

    public function instructores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Instructor::class, 'usuario_id');
    }

    public function suscripciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Suscripcion::class, 'usuario_id');
    }

    public function pagos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pago::class, 'usuario_id');
    }

    public function asistencias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asistencia::class, 'socio_id');
    }

    public function rutinas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rutina::class, 'socio_id');
    }
}
