<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            [
                'email' => 'admin@rym.com',
                'name' => 'Administrador',
                'telefono' => 77012345,
                'codigo' => 'admin',
                'password' => Hash::make('secret'),
                'estado' => 'a'
            ],

        ]);
    }
}
