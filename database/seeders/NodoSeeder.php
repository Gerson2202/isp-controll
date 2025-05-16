<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nodos')->insert([
            [
                'nombre' => 'Sabana',
                'ip' => '192.168.23.1',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
