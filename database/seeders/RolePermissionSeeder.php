<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero, limpiamos las tablas
        \DB::table('role_has_permissions')->truncate();
        \DB::table('model_has_roles')->truncate();
        \DB::table('roles')->truncate();
        \DB::table('permissions')->truncate();

        // Creamos los roles
        $admin = Role::create(['name' => 'Administrador']);
        $vendedor = Role::create(['name' => 'Vendedor']);

        // Creamos los permisos
        $crearSolicitud = Permission::create(['name' => 'crear solicitud']);
        $verSolicitud = Permission::create(['name' => 'ver solicitud']);

        // Asignamos permisos a los roles
        $admin->givePermissionTo([$crearSolicitud, $verSolicitud]);
        $vendedor->givePermissionTo($crearSolicitud);
    }
}
