<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudEjecutada extends Model
{
    public $timestamps = false;

    protected $table = 'solicitudes_ejecutadas';

    protected $fillable = ['solicitud_id', 'ejecutado_por', 'fecha_ejecucion'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ejecutado_por');
    }

}
