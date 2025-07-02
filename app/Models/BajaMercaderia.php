<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BajaMercaderia extends Model
{
    use HasFactory;

    protected $table = 'solicitud_bajas_mercaderia';

    protected $fillable = [
        'id_solicitud', 'almacen', 'detalle_productos', 'estado', 'motivo', 'tipo', 'id_autorizador'. 'fecha_autorizacion'
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

}
