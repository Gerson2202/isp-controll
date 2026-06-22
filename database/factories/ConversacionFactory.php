<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversacionFactory extends Factory
{
    public function definition()
    {
        $estados = ['abierto', 'ia', 'agente', 'cerrado'];
        $estado = $this->faker->randomElement($estados);
        
        return [
            'cliente_id' => Cliente::inRandomOrder()->first()?->id ?? null,
            'telefono' => $this->faker->phoneNumber(),
            'nombre_contacto' => $this->faker->name(),
            'estado' => $estado,
            'ia_activa' => $this->faker->boolean(70), // 70% activa
            'asignado_a' => $estado === 'agente' ? User::inRandomOrder()->first()?->id : null,
            'ultima_actividad' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }
}