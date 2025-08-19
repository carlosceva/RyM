<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudPrecioEspecial extends Model
{
    use HasFactory;

    protected $table = 'solicitud_precio_especial';

    protected $fillable = [
        'id_solicitud', 'cliente', 'detalle_productos', 'estado', 'venta_realizada',
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

}
