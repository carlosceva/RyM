<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class BonificacionPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Supra Administrador']);

        $acciones = ['ver', 'crear', 'aprobar', 'reprobar', 'ejecutar', 'borrar'];

        foreach ($acciones as $accion) {
            $permiso = Permission::firstOrCreate([
                'name' => "Bonificacion_{$accion}",
            ]);

            $adminRole->givePermissionTo($permiso);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
