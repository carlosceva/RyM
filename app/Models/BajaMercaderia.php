<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BajaMercaderia extends Model
{
    use HasFactory;

    protected $table = 'solicitud_bajas_mercaderia';

    protected $fillable = [
        'id_solicitud', 'detalle_productos', 'estado', 'motivo', 'tipo', 'id_autorizador'. 'fecha_autorizacion', 'id_almacen'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function autorizador()
    {
        return $this->belongsTo(User::class, 'id_autorizador');
    }

    public function almacen_nombre()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

     public function encargado()
    {
        return $this->almacen_nombre?->encargado;
    }

    public function esEncargado(User $usuario)
    {
        return $this->encargado && $this->encargado->id === $usuario->id;
    }
}
