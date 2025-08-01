<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'estado',
        'codigo',
        'telefono',
        'name',
        'key'
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

    public function username()
    {
        return 'codigo';
    }

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = strtolower($value);
    }

    public function almacenesEncargados()
    {
        return $this->hasMany(\App\Models\Almacen::class, 'id_encargado');
    }

    public function esEncargadoDeAlmacen(): bool
    {
        return $this->almacenesEncargados()->exists();
    }

    public function notificacionesLocales()
    {
        return $this->hasMany(NotificacionLocal::class);
    }

    public function notificacionesLocalesNoLeidas()
    {
        return $this->notificacionesLocales()->noLeidas();
    }

    public function notificacionesLocalesLeidas()
    {
        return $this->notificacionesLocales()->leidas();
    }

}
