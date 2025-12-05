<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consumible;

class ConsumibleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear consumibles específicos para ISP
        $consumibles = [
            // Fibra Óptica
            [
                'nombre' => 'Carrete fibra óptica SM',
                'descripcion' => 'Carrete de fibra óptica monomodo 12 fibras, 100 metros para tendido aéreo y subterráneo.',
                'unidad' => 'rollos'
            ],
            [
                'nombre' => 'Pigtail LC UPC',
                'descripcion' => 'Pigtail de fibra óptica monomodo con conector LC UPC para armarios de distribución.',
                'unidad' => 'unidades'
            ],
            [
                'nombre' => 'Patchcord LC/LC 3m',
                'descripcion' => 'Cable de parcheo LC/LC monomodo 3 metros para conexión entre equipos OLT y splitter.',
                'unidad' => 'unidades'
            ],
            [
                'nombre' => 'Cassette 1U 12 puertos',
                'descripcion' => 'Cassette de empalme 1U con 12 puertos LC para organización en rack.',
                'unidad' => 'unidades'
            ],

            // Red y Datos
            [
                'nombre' => 'Conector RJ45 Cat6',
                'descripcion' => 'Conector RJ45 categoría 6 blindado para cable UTP 23AWG.',
                'unidad' => 'paquetes'
            ],
            [
                'nombre' => 'Cable UTP Cat6',
                'descripcion' => 'Cable UTP categoría 6 23AWG para instalaciones internas de red.',
                'unidad' => 'metros'
            ],
            [
                'nombre' => 'Conector F compresión',
                'descripcion' => 'Conector F tipo compresión para cable coaxial RG6.',
                'unidad' => 'paquetes'
            ],

            // Fijación
            [
                'nombre' => 'Tornillo poste 1/4"',
                'descripcion' => 'Tornillo para postes de telecomunicaciones zincado 1/4" x 2".',
                'unidad' => 'paquetes'
            ],
            [
                'nombre' => 'Abrazadera cable 10mm',
                'descripcion' => 'Abrazadera de nylon para sujeción de cables diámetro 10mm.',
                'unidad' => 'paquetes'
            ],
            [
                'nombre' => 'Taco wallplug 8mm',
                'descripcion' => 'Taco plástico wallplug para tornillos 8mm en concreto.',
                'unidad' => 'paquetes'
            ],

            // Herramientas
            [
                'nombre' => 'Pelacables triple',
                'descripcion' => 'Herramienta pelacables para coaxial, UTP y fibra óptica.',
                'unidad' => 'unidades'
            ],
            [
                'nombre' => 'Crimpadora RJ45/RJ11',
                'descripcion' => 'Crimpadora profesional para conectores RJ45 y RJ11.',
                'unidad' => 'unidades'
            ],

            // Protección
            [
                'nombre' => 'Caja terminal exterior',
                'descripcion' => 'Caja terminal para exterior IP65 con capacidad para 4 fusiones.',
                'unidad' => 'unidades'
            ],
            [
                'nombre' => 'Tubo termoretráctil 40mm',
                'descripcion' => 'Tubo termoretráctil diámetro 40mm para protección de empalmes.',
                'unidad' => 'metros'
            ]
        ];

        foreach ($consumibles as $consumible) {
            Consumible::create($consumible);
        }

        // Opcional: Crear consumibles adicionales con Factory
        Consumible::factory()->count(10)->create();
    }
}