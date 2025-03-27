<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'estado' => 'a',  // Activo
                'telefono' => '77012345',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
