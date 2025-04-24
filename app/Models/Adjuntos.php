<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjuntos extends Model
{
    // Definir la tabla si no sigue la convención de pluralización
    protected $table = 'adjuntos';

    // Si el campo 'id' es auto incremental, no es necesario definirlo
    protected $primaryKey = 'id';

    // Si no deseas que Laravel gestione los timestamps
    public $timestamps = true;

    // Define los campos que pueden ser llenados de forma masiva (mass assignable)
    protected $fillable = ['id_solicitud', 'archivo'];
}
