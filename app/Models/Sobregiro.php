<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sobregiro extends Model
{
    use HasFactory;

    protected $table = 'solicitud_sobregiro';

    protected $fillable = [
        'id_solicitud', 'cliente', 'importe', 'estado'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
