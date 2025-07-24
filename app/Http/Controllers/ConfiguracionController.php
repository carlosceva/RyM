<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $notificacionesTwilio = Configuracion::where('clave', 'notificaciones_twilio')->value('valor') ?? '0';

        return view('Administracion.configuracion.index', compact('notificacionesTwilio'));
    }

    public function actualizarTwilio(Request $request)
    {
        $nuevoValor = $request->input('estado') ? '1' : '0';

        Configuracion::updateOrCreate(
            ['clave' => 'notificaciones_twilio'],
            ['valor' => $nuevoValor]
        );

        return response()->json(['success' => true]);
    }
}
