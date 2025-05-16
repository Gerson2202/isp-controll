<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
{
    DB::table('users')->insert([
        [
            'name' => 'Gerson PSJ',
            'email' => 'gersonpsj@gmail.com',
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Enio Peñaloza',
            'email' => 'EnioP@gmail.com',
            'password' => Hash::make('12345678'), // Cambia por una contraseña segura
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'), // Cambia por una contraseña segura
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
}
}
