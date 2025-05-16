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
                'nombre' => 'CerroCapote',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7185,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sabana',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7186,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Portico',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7187,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Chicaro',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7188,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Pabellon',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7189,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Epon_Fibra_Sabana',
                'ip' => '45.65.136.244',
                'latitud' => 7.8939,   // Coordenada de Cúcuta
                'longitud' => -72.5071,
                'puerto_api' => 7190,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
