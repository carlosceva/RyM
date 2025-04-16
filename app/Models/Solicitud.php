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

    // Relación con la solicitud de precio especial
    public function solicitudPrecioEspecial()
    {
        return $this->hasOne(SolicitudPrecioEspecial::class, 'id_solicitud');
    }
    // Relación con el cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function precioEspecial()
{
    return $this->hasOne(SolicitudPrecioEspecial::class, 'id_solicitud');
}
}
