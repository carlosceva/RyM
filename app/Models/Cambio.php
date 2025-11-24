<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cambio extends Model
{
    use HasFactory;

    protected $table = 'solicitud_cambios_mercaderia';

    protected $fillable = [
        'id_solicitud', 'nota_venta', 'id_almacen', 'detalle_productos', 'motivo', 'estado',  
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
