<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),  // Nombre del cliente
            'telefono' => $this->faker->phoneNumber(),  // Número de teléfono
            'direccion' => $this->faker->address(),  // Dirección
            'correo' => $this->faker->unique()->safeEmail(),  // Correo electrónico
            'cedula' => $this->faker->unique()->numerify('###########'),  // Cédula (números aleatorios)
            'estado' => 'cortado',
        ];
    }
}
