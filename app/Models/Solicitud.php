<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitud extends Model
{
    use HasFactory;
    
    protected $table = 'solicitudes';

    protected $fillable = [
        'id_usuario', 'tipo', 'fecha_solicitud', 'estado', 'id_autorizador', 'fecha_autorizacion', 'glosa', 'id_cliente', 'detalle_productos', 'observacion'
    ];

    // Relación con el usuario que solicita
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con el usuario que autoriza
    public function autorizador()
    {
        return $this->belongsTo(User::class, 'id_autorizador');
    }

    public function precioEspecial()
    {
        return $this->hasOne(SolicitudPrecioEspecial::class, 'id_solicitud');
    }

    public function muestraMercaderia()
    {
        return $this->hasOne(MuestraMercaderia::class, 'id_solicitud');
    }

    public function bajaMercaderia()
    {
        return $this->hasOne(BajaMercaderia::class, 'id_solicitud');
    }

    public function sobregiro()
    {
        return $this->hasOne(Sobregiro::class, 'id_solicitud');
    }

    public function anulacion()
    {
        return $this->hasOne(Anulacion::class, 'id_solicitud');
    }

    public function devolucion()
    {
        return $this->hasOne(Devolucion::class, 'id_solicitud');
    }

    public function ejecucion()
    {
        return $this->hasOne(SolicitudEjecutada::class, 'solicitud_id');
    }

    public function ejecuciones()
    {
        return $this->hasMany(SolicitudEjecutada::class, 'solicitud_id');
    }

    public function adjuntos()
    {
        return $this->hasMany(Adjuntos::class, 'id_solicitud');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadasNoEjecutadas($query)
    {
        return $query->where('estado', 'aprobada')
                    ->whereDoesntHave('ejecucion');
    }

    public function cambioMercaderia()
    {
        return $this->hasOne(Cambio::class, 'id_solicitud');
    }

    public function extra()
    {
        return $this->hasOne(Extras::class, 'id_solicitud');
    }

    public function vacacion()
    {
        return $this->hasOne(Vacaciones::class, 'id_solicitud');
    }

}
