<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anulacion extends Model
{
    use HasFactory;

    protected $table = 'solicitud_anulacion';

    protected $fillable = [
        'id_solicitud', 'nota_venta', 'motivo', 'estado', 'tiene_pago', 'obs_pago', 'tiene_entrega', 'entrega_fisica', 'id_almacen'
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

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    public function encargado()
    {
        return $this->almacen?->encargado;
    }

    public function esEncargado(User $usuario)
    {
        return $this->encargado && $this->encargado->id === $usuario->id;
    }

}
