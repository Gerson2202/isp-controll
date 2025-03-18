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
                'nombre' => 'Cúcuta',
                'ip' => '192.168.1.1',
                // 'user' => 'GerApi',
                // 'pass' => '12345678',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                // 'puerto_api' => '8728',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ocaña',
                'ip' => '192.168.1.2',
                // 'user' => 'GerApi',
                // 'pass' => '12345678',
                'latitud' => 8.2296,   // Coordenada de Ocaña
                'longitud' => -73.3230,
                // 'puerto_api' => '8728',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ábrego',
                'ip' => '192.168.1.3',
                // 'user' => 'GerApi',
                // 'pass' => '12345678',
                'latitud' => 8.2118,   // Coordenada de Ábrego
                'longitud' => -73.1486,
                // 'puerto_api' => '8728',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'La Playa',
                'ip' => '192.168.1.4',
                // 'user' => 'GerApi',
                // 'pass' => '12345678',
                'latitud' => 8.4019,   // Coordenada de La Playa
                'longitud' => -72.7625,
                // 'puerto_api' => '8728',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tibú',
                'ip' => '192.168.1.5',
                // 'user' => 'GerApi',
                // 'pass' => '12345678',
                'latitud' => 8.2997,   // Coordenada de Tibú
                'longitud' => -73.5136,
                // 'puerto_api' => '8728',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
