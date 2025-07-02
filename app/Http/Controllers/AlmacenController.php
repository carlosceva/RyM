<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;
use App\Models\User;

class AlmacenController extends Controller
{
public function index()
    {
        $almacenes = Almacen::where('estado', 'a')->orderBy('id', 'desc')->get();

        $usuarios = User::where('estado', 'a')->get(); 

        return view('Administracion.almacenes.index', compact('almacenes', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_encargado' => 'nullable|exists:users,id,estado,a',
        ]);

        Almacen::create([
            'nombre' => $request->nombre,
            'estado' => 'a', // activo por defecto
            'id_encargado' => $request->id_encargado,
        ]);

        return redirect()->route('almacen.index')->with('success', 'Almacén creado correctamente.');
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_encargado' => 'nullable|exists:users,id,estado,a',
        ]);

        $almacen->update([
            'nombre' => $request->nombre,
            'id_encargado' => $request->id_encargado,
        ]);

        return redirect()->route('almacen.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        // Eliminación lógica, cambiar estado a 'i'
        $almacen->update(['estado' => 'i']);

        return redirect()->route('almacen.index')->with('success', 'Almacén desactivado correctamente.');
    }
}
