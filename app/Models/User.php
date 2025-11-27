<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles;

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
        'especialidades',
        'biografia',
        'url_qr',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }

    public function mediciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MedicionProgreso::class, 'socio_id');
    }

    // Relación como instructor
    public function horarios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Horario::class, 'instructor_id');
    }

    // Relación como instructor
    public function rutinas_instructor(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rutina::class, 'instructor_id');
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
