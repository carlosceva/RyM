<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MuestraMercaderia extends Model
{
    use HasFactory;

    protected $table = 'solicitud_muestras_mercaderia';

    protected $fillable = [
        'id_solicitud', 'cliente', 'detalle_productos', 'estado','cod_sai', 'id_almacen'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    // Función para obtener al encargado del almacén
    public function encargado()
    {
        return $this->almacen->encargado;  // Accede al encargado del almacén relacionado
    }

    // Función para verificar si un usuario es el encargado
    public function esEncargado(User $usuario)
    {
        return $this->encargado && $this->encargado->id === $usuario->id;
    }
}
