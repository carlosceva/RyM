<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonificacion extends Model
{
    use HasFactory;

    protected $table = 'solicitud_bonificaciones';

    protected $fillable = [
        'id_solicitud',
        'nota_venta',
        'beneficiario',
        'telefono',
        'monto',
        'estado',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
