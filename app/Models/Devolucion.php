<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'solicitud_devolucion';

    protected $fillable = [
        'id_solicitud', 'nota_venta', 'motivo', 'estado','detalle_productos','almacen','cliente','tiene_pago','tiene_entrega', 'obs_pago', 'entrega_fisica'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
