<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        $roles = Role::all();
        return view('Administracion.usuarios.index', compact('usuarios', 'roles'));
    }

    public function asignarRol(Request $request, User $user)
    {
        $request->validate([
            'rol' => 'required|exists:roles,name',
        ]);
    
        $user->syncRoles([$request->rol]);
    
        return redirect()->route('usuario.index')->with('success', 'Rol asignado correctamente.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
            
            $usuario = User::create([
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'estado' => 'a',
                'codigo' => $request->input('codigo'),
                'name' => $request->input('name'),
                'telefono' => $request->input('telefono'),
            ]);

            $usuario->save();

            Session::flash('success', 'Usuario agregado exitosamente.');
        } catch (\Exception $e) {
            Session::flash('error', 'Ocurrió un error al guardar usuario.');
        }
    
        return redirect()->route('usuario.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*$request->validate([
            'name' => 'required|string|max:255',
            'telefono' => 'required|integer',
        ]);*/

        $usuario = User::findOrFail($id);
        $usuario->update($request->all());

        return redirect()->route('usuario.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'i']);

        return redirect()->route('usuario.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
