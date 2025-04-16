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

    }
}
