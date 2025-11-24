<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddExtraConfirmarPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Supra Administrador']);

        $permiso = Permission::firstOrCreate([
            'name' => 'Extra_confirmar',
        ]);

        $adminRole->givePermissionTo($permiso);
    }
}
