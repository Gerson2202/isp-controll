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
            'nombre' => $this->faker->name(),
            'telefono' => '573215852059',
            'direccion' => $this->faker->address(),
            'correo' => $this->faker->unique()->safeEmail(),
            'cedula' => $this->faker->unique()->numerify('###########'),
            'estado' => 'activo',
            'ip' => '192.168.5.' . $this->faker->unique()->numberBetween(1, 254),
        ];
    }
}
