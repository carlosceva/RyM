<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // $solicitudes = ['Precio_especial', 'Anulacion', 'Devolucion', 'Sobregiro', 'Baja', 'Muestra'];
        // $acciones = ['ver', 'crear', 'editar', 'borrar', 'aprobar', 'reprobar'];
        
        // foreach ($solicitudes as $solicitud) {
        //     foreach ($acciones as $accion) {
        //         $permiso = "{$solicitud}_{$accion}";
        //         Permission::firstOrCreate(['name' => $permiso]);
        //     }
        // }

        $permisosAdmin = [
            // Usuarios
            'usuarios_ver',
            'usuarios_crear',
            'usuarios_editar',
            'usuarios_borrar',
            
            // Roles
            'roles_ver',
            'roles_crear',
            'roles_editar',
            'roles_borrar',

            // Permisos
            'permisos_ver',
        ];

        // Crear los permisos si no existen
        foreach ($permisosAdmin as $permisoAdmin) {
            Permission::firstOrCreate(['name' => $permisoAdmin]);
        }

        // 🧑‍💼 Crear el rol de administrador si no existe
        //$adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // Asignar todos los permisos al rol de administrador
        //$adminRole->syncPermissions(Permission::all());


    }
}
