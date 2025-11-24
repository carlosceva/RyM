<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extras extends Model
{
    use HasFactory;

    protected $table = 'solicitud_extras';

    protected $fillable = [
        'id_solicitud', 'tipo_extra', 'fecha_solicitud', 'estado', 'id_confirmador', 'fecha_confirmacion'
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

    public function confirmador()
    {
        return $this->belongsTo(User::class, 'id_confirmador');
    }
}