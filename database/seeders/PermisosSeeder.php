<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'estado' => 'a']);

        // $solicitudes = ['Precio_especial', 'Anulacion', 'Devolucion', 'Sobregiro', 'Baja', 'Muestra'];
        // $acciones = ['ver', 'crear', 'borrar', 'aprobar', 'reprobar', 'ejecutar'];
        
        // foreach ($solicitudes as $solicitud) {
        //     foreach ($acciones as $accion) {
        //         $permiso = "{$solicitud}_{$accion}";
        //         $permisoObj = Permission::firstOrCreate(['name' => $permiso]);

        //         // Asigna el permiso al rol "Administrador"
        //         $adminRole->givePermissionTo($permisoObj);
        //     }
        // }

        // $permisosAdmin = [
        //     // Usuarios
        //     'usuarios_ver',
        //     'usuarios_crear',
        //     'usuarios_editar',
        //     'usuarios_borrar',
            
        //     // Roles
        //     'roles_ver',
        //     'roles_crear',
        //     'roles_editar',
        //     'roles_borrar',

        //     // Permisos
        //     'permisos_ver',

        //     //entrega y pago
        //     'Anulacion_entrega',
        //     'Anulacion_pago',
        //     'Devolucion_entrega',
        //     'Devolucion_pago'
        // ];

        // // Crear los permisos si no existen
        // foreach ($permisosAdmin as $permisoAdmin) {
        //     $permisoObj = Permission::firstOrCreate(['name' => $permisoAdmin]);

        //     // Asigna el permiso al rol "Administrador"
        //     $adminRole->givePermissionTo($permisoObj);
        // }

        //los ejecutar
        $ejecutarPermisos = [
            'Anulacion_ejecutar',
            'Devolucion_ejecutar',
            'Precio_especial_ejecutar', 
            'Sobregiro_ejecutar', 
            'Baja_ejecutar', 
            'Muestra_ejecutar'
        ];

        foreach ($ejecutarPermisos as $nuevos) {
            $permisoObj = Permission::firstOrCreate(['name' => $nuevos]);

            $adminRole->givePermissionTo($permisoObj);
        }

        // 🧑‍💼 Crear el rol de administrador si no existe
        //$adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // Asignar todos los permisos al rol de administrador
        //$adminRole->syncPermissions(Permission::all());
    }
}
