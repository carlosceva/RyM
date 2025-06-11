<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Borrar todos los datos de la tabla users antes de insertar
        DB::table('users')->truncate();

        // Crear el usuario
        $user = DB::table('users')->insertGetId([
            'email' => 'admin@rym.com',
            'name' => 'Supra Administrador',
            'telefono' => 77012345,
            'codigo' => 'supra',
            'password' => Hash::make('secret'),
            'estado' => 'a'
        ]);

        // Obtener el rol de Administrador
        $adminRole = Role::firstOrCreate(['name' => 'Supra Administrador', 'estado' => 'a']);

        // Encontrar el usuario recién creado
        $user = User::find($user);

        // Asignar el rol al usuario
        $user->assignRole($adminRole);
    }
}
