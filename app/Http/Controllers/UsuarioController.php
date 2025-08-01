<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        $roles = Role::where('estado','a')->get();
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
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'telefono' => 'required|numeric',
            'codigo' => 'required|string|unique:users,codigo',
            'rol' => 'required|exists:roles,name', // Validación del rol
        ]);
        
        try {
            
            $usuario = User::create([
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'estado' => 'a',
                'codigo' => $request->input('codigo'),
                'name' => $request->input('name'),
                'telefono' => $request->input('telefono'),
            ]);

            // Asignar rol
            $usuario->assignRole($request->rol);

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
        // Validación de los campos necesarios
        $request->validate([
            'name' => 'required|string|max:255',
            'telefono' => 'required|numeric', // Validamos que el teléfono sea un número
            'email' => 'email|max:255',
            'estado' => 'required|string|in:a,i',
            // Validación para la contraseña (opcional)
            'password' => 'nullable|string|min:6|confirmed', // La contraseña es opcional, pero si se proporciona, debe tener confirmación
            'rol' => 'nullable|exists:roles,name',
        ]);
    
        // Encontrar al usuario
        $usuario = User::findOrFail($id);
    
        // Actualizar los datos del usuario (excepto la contraseña)
        $usuario->codigo = $request->codigo;
        $usuario->name = $request->name;
        $usuario->telefono = $request->telefono;
        $usuario->email = $request->email;
        $usuario->estado = $request->estado;
    
        // Si la contraseña es proporcionada, la actualizamos
        if ($request->has('password') && !empty($request->password)) {
            $usuario->password = Hash::make($request->password);
        }

        // Actualizar el rol si fue seleccionado
        if ($request->filled('rol')) {
            $usuario->syncRoles([$request->rol]); // Se asigna el rol seleccionado
        } else {
            $usuario->syncRoles([]); // Se elimina cualquier rol asignado (usuario sin rol)
        }

        // Guardar los cambios en la base de datos
        $usuario->save();
    
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
