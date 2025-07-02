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
        $role = Role::findOrFail($roleId);
        $grupo = $request->input('grupo'); // 'solicitudes' o 'otros'
        $nuevosPermisos = $request->input('permisos', []);

        $solicitudes = ['Anulacion', 'Devolucion', 'Precio_especial', 'Sobregiro', 'Baja', 'Muestra'];
        $acciones = ['ver', 'crear', 'editar', 'borrar', 'aprobar', 'reprobar', 'ejecutar', 'entrega', 'pago'];

        // Obtener todos los permisos del grupo "solicitudes"
        $permisosSolicitudes = collect();

        foreach ($solicitudes as $solicitud) {
            foreach ($acciones as $accion) {
                $nombre = strtolower("{$solicitud}_{$accion}");
                $permiso = Permission::whereRaw('LOWER(name) = ?', [$nombre])->first();
                if ($permiso) {
                    $permisosSolicitudes->push($permiso->id);
                }
            }
        }

        // Separar según grupo
        if ($grupo === 'solicitudes') {
            // Mantener los permisos que NO son de solicitudes
            $permisosRestantes = $role->permissions->pluck('id')->diff($permisosSolicitudes);
        } elseif ($grupo === 'otros') {
            // Mantener solo los permisos que SON de solicitudes
            $permisosRestantes = $role->permissions->pluck('id')->intersect($permisosSolicitudes);
        } else {
            $permisosRestantes = collect(); // Fallback por seguridad
        }

        // Combinar los que vienen con los que ya tenía fuera del grupo
        $permisosFinales = $permisosRestantes->merge($nuevosPermisos)->unique();

        // Asignar
        $role->permissions()->sync($permisosFinales);

        // Limpiar cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('permisos.index')->with('success', 'Permisos asignados correctamente.');
    }
}
