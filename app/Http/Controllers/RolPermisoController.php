<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolPermisoController extends Controller
{
    // Mostrar todos los roles con sus permisos
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('Administracion.permisos.index', compact('roles', 'permissions'));
    }

    // Guardar los permisos seleccionados para un rol
    public function guardar(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId); // Buscar el rol
        $permissions = $request->input('permisos', []); // Asegúrate que el name del checkbox sea "permisos[]"

        $role->permissions()->sync($permissions); // ✅ Aquí se espera un array de IDs

        // Limpiar la caché de permisos automáticamente
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permisos.index')->with('success', 'Permisos asignados correctamente.');
    }

}
