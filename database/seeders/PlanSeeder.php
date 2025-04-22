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
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-1',
                'descripcion' => 'Clientes horario',
                // 'precio' => 80.000,
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                // 'precio' => 10.000,
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-8-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                // 'precio' => 150.000,
                'velocidad_bajada' => 8,
                'velocidad_subida' => 8,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-6-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                // 'precio' => 200.000,
                'velocidad_bajada' => 6,
                'velocidad_subida' => 6,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
