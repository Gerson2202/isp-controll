<?php

namespace Database\Seeders;

use App\Models\Contrato;
use App\Models\Plan;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los clientes y planes
        $clientes = Cliente::all();
        $planes = Plan::all();

        // Validar que existan planes
        if ($planes->isEmpty()) {
            $this->command->error('No hay planes registrados. Crea al menos 3 planes primero.');
            return;
        }

        // Crear 1 contrato activo por cliente
        $clientes->each(function ($cliente) use ($planes) {
            $fechaInicio = now()->subMonths(rand(1, 12));
            
            Contrato::create([
                'cliente_id' => $cliente->id,
                'plan_id' => $planes->random()->id,
                'tecnologia' => $this->getTecnologiaAleatoria(),
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaInicio->copy()->addYear(), // Fecha fin = 1 año después
                'estado' => 'activo',
                // CAMBIO AQUÍ: Valores reales en Pesos Colombianos (COP)
                'precio' => collect([60000, 100000, 150000])->random(),              
                'created_at' => now(),
            ]);
        });

        $this->command->info("Se crearon {$clientes->count()} contratos activos.");
    }

    private function getTecnologiaAleatoria(): string
    {
        $tecnologias = ['Fibra óptica', 'Radiofrecuencia', 'Satelital'];
        return $tecnologias[array_rand($tecnologias)];
    }

    private function getPrecioSegunPlan(Plan $plan): int
    {
        // Nota: Si vas a usar este método en el futuro, cámbialo también a valores COP
        return $plan->precio_base + rand(20000, 50000);
    }
}