<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sobregiro extends Model
{
    use HasFactory;

    protected $table = 'solicitud_sobregiro';

    protected $fillable = [
        'id_solicitud', 'cliente', 'importe', 'estado', 'cod_sobregiro', 'id_confirmador', 'fecha_confirmacion'
    ];

    // Relación con la solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function confirmador()
    {
        return $this->belongsTo(User::class, 'id_confirmador');
    }
}
