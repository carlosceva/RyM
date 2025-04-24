<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MuestraMercaderia extends Model
{
    use HasFactory;

    protected $table = 'solicitud_muestras_mercaderia';

    protected $fillable = [
        'id_solicitud', 'cliente', 'detalle_productos', 'estado','cod_sai'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
