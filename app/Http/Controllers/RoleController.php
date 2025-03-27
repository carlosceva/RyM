<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('Administracion.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        /*$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telefono' => 'required|integer',
            'rol'=>'required',
        ]);*/
        
        try {
            
            $rol = Role::create([
                'name' => $request->input('nombre'),
                'guard_name'=>'web',
                'estado' => 'a',
            ]);

            $rol->save();

            Session::flash('success', 'Rol agregado exitosamente.');
        } catch (\Exception $e) {
            Session::flash('error', 'Ocurrió un error al guardar Rol.');
        }
    
        return redirect()->route('roles.index');
    }

    public function update(Request $request, string $id)
    {
        /*$request->validate([
            'name' => 'required|string|max:255',
            'telefono' => 'required|integer',
        ]);*/

        $rol = Role::findOrFail($id);
        $rol->update($request->all());

        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy( $id)
    {
        $rol = Role::findOrFail($id);
        $rol->update(['estado' => 'i']);

        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}

