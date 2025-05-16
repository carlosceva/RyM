<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserProfileController extends Controller
{
    // Muestra el perfil del usuario
    public function show()
    {
        // Aquí se obtiene el usuario autenticado
        return view('profile.show', [
            'user' => Auth::user(), // Pasamos el usuario a la vista
        ]);
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(), // Pasamos el usuario a la vista
        ]);
    }

    public function update(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'telefono' => 'required|numeric',
            'codigo' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Actualizar los datos del usuario
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'codigo' => $request->codigo,
            'estado' => $request->estado,
        ]);

        // Redirigir al perfil con un mensaje de éxito
        return redirect()->route('profile.show')->with('success', 'Información actualizada correctamente.');
    }

    public function changePassword(Request $request)
    {
        // Validar las contraseñas
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return back()->withErrors(['old_password' => 'La contraseña actual no es correcta.']);
        }

        // Cambiar la contraseña
        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Contraseña cambiada con éxito.');
    }
}

