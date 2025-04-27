<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anulacion extends Model
{
    use HasFactory;

    protected $table = 'solicitud_anulacion';

    protected $fillable = [
        'id_solicitud', 'nota_venta', 'motivo', 'estado'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
