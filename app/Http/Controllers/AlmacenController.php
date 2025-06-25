<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
public function index()
    {
        $almacenes = Almacen::where('estado', 'a')->orderBy('id', 'desc')->get();
        return view('Administracion.almacenes.index', compact('almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Almacen::create([
            'nombre' => $request->nombre,
            'estado' => 'a', // activo por defecto
        ]);

        return redirect()->route('almacen.index')->with('success', 'Almacén creado correctamente.');
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $almacen->update([
            'nombre' => $request->nombre,
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
