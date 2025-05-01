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
                'precio' => collect([16, 23, 30])->random(),               
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
        // Ejemplo: Precio base del plan + variación aleatoria
        return $plan->precio_base + rand(20000, 200000);
    }
}
