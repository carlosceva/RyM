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

    protected $casts = [
        'tiene_entrega' => 'boolean',
        'tiene_pago' => 'boolean',
        'entrega_fisica' => 'boolean',
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function almacen_nombre()
    {
        return $this->belongsTo(Almacen::class, 'almacen'); 
    }

    public function encargado()
    {
        return $this->almacen_nombre()?->encargado; 
    }


}
