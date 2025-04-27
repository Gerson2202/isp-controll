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

            // Nodo Chicaro
            [
                'nombre' => 'PLAN-8-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 8,
                'velocidad_subida' => 8,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-6-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 6,
                'velocidad_subida' => 6,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-20-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 20,
                'velocidad_subida' => 20,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Nodo Sabana
            [
                'nombre' => 'PLAN-8-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 8,
                'velocidad_subida' => 8,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-6-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 6,
                'velocidad_subida' => 6,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-20-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 20,
                'velocidad_subida' => 20,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Nodo Portico
            [
                'nombre' => 'PLAN-8-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 8,
                'velocidad_subida' => 8,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-6-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 6,
                'velocidad_subida' => 6,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-20-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 20,
                'velocidad_subida' => 20,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Nodo Pabellon 
            [
                'nombre' => 'PLAN-8-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 8,
                'velocidad_subida' => 8,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-10-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 10,
                'velocidad_subida' => 10,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-6-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 6,
                'velocidad_subida' => 6,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'PLAN-20-MEGAS-REHUSO-1-4',
                'descripcion' => 'Clientes horario',
                'velocidad_bajada' => 20,
                'velocidad_subida' => 20,
                'rehuso' => '1:4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
