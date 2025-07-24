<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $fillable = ['clave', 'valor'];

    protected $table = 'configuraciones';

    public static function getValor(string $clave, $default = null)
    {
        return optional(self::where('clave', $clave)->first())->valor ?? $default;
    }
}
