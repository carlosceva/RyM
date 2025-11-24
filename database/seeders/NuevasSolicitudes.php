<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NuevasSolicitudes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtiene o crea el rol Supra Administrador
        $adminRole = Role::firstOrCreate([
            'name' => 'Supra Administrador'
        ]);

        // TIPOS DE SOLICITUDES NUEVAS
        $solicitudes = [
            'Cambio',
            'Extra',
            'Vacaciones',
        ];

        // ACCIONES POR CADA SOLICITUD
        $acciones = [
            'ver',
            'crear',
            'borrar',
            'aprobar',
            'reprobar',
            'ejecutar',
        ];

        foreach ($solicitudes as $solicitud) {
            foreach ($acciones as $accion) {

                $permisoNombre = "{$solicitud}_{$accion}";

                $permiso = Permission::firstOrCreate([
                    'name' => $permisoNombre,
                ]);

                // ASIGNAR PERMISO AL ROL
                $adminRole->givePermissionTo($permiso);
            }
        }

        // Finalmente sincroniza todos los permisos creados con el rol
        $adminRole->syncPermissions(Permission::all());
    }
}
