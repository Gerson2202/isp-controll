<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConsumibleFactory extends Factory
{
    public function definition(): array
    {
        // Unidades actualizadas incluyendo "carrete"
        $unidades = ['metros', 'rollos', 'paquetes', 'unidades', 'cajas', 'pares', 'juegos', 'carretes'];
        
        $categorias = [
            'Fibra Óptica' => ['Carrete fibra óptica', 'Pigtail LC', 'Pigtail SC', 'Patchcord LC/LC', 'Patchcord SC/SC', 'Cassette FO'],
            'Red y Datos' => ['Conector RJ45', 'Cable UTP', 'Conector F', 'Adaptador', 'Divisor'],
            'Fijación' => ['Tornillo autorroscante', 'Taco plástico', 'Abrazadera', 'Soporte', 'Grapa'],
            'Herramientas' => ['Pelacables', 'Crimpadora', 'Probador', 'Destornillador'],
            'Protección' => ['Caja terminal', 'Tubo termoretráctil', 'Cinta aislante', 'Protector']
        ];

        $categoria = array_rand($categorias);
        $nombre = $this->faker->randomElement($categorias[$categoria]);

        return [
            'nombre' => $nombre,
            'descripcion' => $this->generateDescription($nombre, $categoria),
            'unidad' => $this->getUnitForProduct($nombre),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateDescription(string $nombre, string $categoria): string
    {
        $descriptions = [
            'Fibra Óptica' => [
                'Carrete fibra óptica' => 'Carrete de fibra óptica monomodo para instalaciones exteriores, 100 metros de longitud.',
                'Pigtail LC' => 'Pigtail de fibra óptica con conector LC para empalmes en cajas de distribución.',
                'Pigtail SC' => 'Pigtail de fibra óptica con conector SC para conexiones de red.',
                'Patchcord LC/LC' => 'Cable de parcheo LC/LC para conexiones entre equipos activos.',
                'Patchcord SC/SC' => 'Cable de parcheo SC/SC para interconexión de equipos.',
                'Cassette FO' => 'Cassette de empalme para organizar y proteger fusiones de fibra.'
            ],
            'Red y Datos' => [
                'Conector RJ45' => 'Conector RJ45 categoría 6 para terminación de cables de red.',
                'Cable UTP' => 'Cable UTP Cat6 para instalaciones de red interna.',
                'Conector F' => 'Conector F para cable coaxial en instalaciones de TV.',
                'Adaptador' => 'Adaptador para conversión de conectores en instalaciones.',
                'Divisor' => 'Divisor de señal para instalaciones de televisión por cable.'
            ],
            'Fijación' => [
                'Tornillo autorroscante' => 'Tornillo autorroscante para fijación en postes y estructuras metálicas.',
                'Taco plástico' => 'Taco de plástico para fijación en paredes de concreto y ladrillo.',
                'Abrazadera' => 'Abrazadera de nylon para sujeción de cables en postes y paredes.',
                'Soporte' => 'Soporte para fijación de equipos en postes de telecomunicaciones.',
                'Grapa' => 'Grapa cableadora para sujeción de cables en superficies.'
            ],
            'Herramientas' => [
                'Pelacables' => 'Herramienta pelacables para preparación de cables de red y fibra.',
                'Crimpadora' => 'Crimpadora para terminación de conectores RJ45 y RJ11.',
                'Probador' => 'Probador de continuidad para cables de red y fibra óptica.',
                'Destornillador' => 'Destornillador de precisión para equipos de telecomunicaciones.'
            ],
            'Protección' => [
                'Caja terminal' => 'Caja terminal para protección de empalmes y conexiones exteriores.',
                'Tubo termoretráctil' => 'Tubo termoretráctil para protección de empalmes contra humedad.',
                'Cinta aislante' => 'Cinta aislante para protección de conexiones eléctricas.',
                'Protector' => 'Protector para conexiones exteriores contra agentes climáticos.'
            ]
        ];

        return $descriptions[$categoria][$nombre] ?? "Consumible para instalaciones de telecomunicaciones en categoría {$categoria}.";
    }

    private function getUnitForProduct(string $nombre): string
    {
        $unitMapping = [
            // Productos que se miden en carretes
            'Carrete fibra óptica' => 'carretes',
            'Cable UTP' => 'carretes',
            
            // Productos en metros
            'Tubo termoretráctil' => 'metros',
            'Cinta aislante' => 'metros',
            
            // Productos en paquetes
            'Conector RJ45' => 'paquetes',
            'Conector F' => 'paquetes',
            'Tornillo autorroscante' => 'paquetes',
            'Taco plástico' => 'paquetes',
            'Abrazadera' => 'paquetes',
            'Grapa' => 'paquetes',
            
            // Productos en unidades
            'Pigtail LC' => 'unidades',
            'Pigtail SC' => 'unidades',
            'Patchcord LC/LC' => 'unidades',
            'Patchcord SC/SC' => 'unidades',
            'Cassette FO' => 'unidades',
            'Adaptador' => 'unidades',
            'Divisor' => 'unidades',
            'Soporte' => 'unidades',
            'Pelacables' => 'unidades',
            'Crimpadora' => 'unidades',
            'Probador' => 'unidades',
            'Destornillador' => 'unidades',
            'Caja terminal' => 'unidades',
            'Protector' => 'unidades'
        ];

        return $unitMapping[$nombre] ?? $this->faker->randomElement(['unidades', 'paquetes', 'carretes']);
    }
}
