<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::create([
            'nombre' => 'SILCOM Telecomunicaciones',
            'slogan' => 'Conectando tu mundo',
            'nit' => '900123456-7',
            'logo' => 'logos/silcom.png',
            'telefono' => '3001234567',
            'email' => 'contacto@silcom.com',
            'direccion' => 'Calle 10 #20-30',
            'ciudad' => 'Cúcuta',
            'correo' => 'soporte@silcom.com',
            'descripcion' => 'Proveedor de servicios de internet especializado en fibra óptica y conectividad empresarial.'
        ]);
    }
}
