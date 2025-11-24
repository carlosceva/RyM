<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacaciones extends Model
{
    use HasFactory;

    protected $table = 'solicitud_vacaciones';

    protected $fillable = [
        'id_solicitud', 'fecha_inicial', 'fecha_fin', 'estado',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }

    public function autorizador()
    {
        return $this->belongsTo(User::class, 'id_autorizador');
    }
}
