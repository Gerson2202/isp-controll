<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar 4 planes con el campo rehuso
        DB::table('plans')->insert([
            [
                'nombre' => 'PLAN 10 MEGAS',
                'descripcion' => 'Clientes horario',
                // 'precio' => 80.000,
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN 20 MEGAS',
                'descripcion' => 'Clientes horario',
                // 'precio' => 10.000,
                'velocidad_bajada' => 20,
                'velocidad_subida' => 20,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN 50 MEGAS',
                'descripcion' => 'Clientes horario',
                // 'precio' => 150.000,
                'velocidad_bajada' => 50,
                'velocidad_subida' => 50,
                'rehuso' => '1:6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN 100 MEGAS',
                'descripcion' => 'Clientes horario',
                // 'precio' => 200.000,
                'velocidad_bajada' => 100,
                'velocidad_subida' => 100,
                'rehuso' => '1:1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
