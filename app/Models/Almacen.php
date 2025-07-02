<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen';

    protected $fillable = ['nombre', 'estado', 'id_encargado'];

    public function encargado()
    {
        return $this->belongsTo(User::class, 'id_encargado');
    }
}
