<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pool; // <-- Asegúrate de importar el modelo

class PoolSeeder extends Seeder
{
    public function run(): void
    {
        Pool::create([
            'nodo_id'    => 2, // Nodo sabana
            'nombre'     => 'clientes-pool',
            'descripcion'=> 'Pool principal para Router de clientes',
            'start_ip'   => '192.168.21.2',
            'end_ip'     => '192.168.21.248',
        ]);

        Pool::create([
            'nodo_id'    => 3, // Nodo Portico
            'nombre'     => 'clientes-pool',
            'descripcion'=> 'Pool principal para Router de clientes',
            'start_ip'   => '192.168.22.2',
            'end_ip'     => '192.168.22.248',
        ]);
        Pool::create([
            'nodo_id'    => 4, // Nodo chicaro
            'nombre'     => 'clientes-pool',
            'descripcion'=> 'Pool principal para Router de clientes',
            'start_ip'   => '192.168.24.2',
            'end_ip'     => '192.168.24.248',
        ]);
        Pool::create([
            'nodo_id'    =>5, // Nodo Pabellon
            'nombre'     => 'clientes-pool',
            'descripcion'=> 'Pool principal para Router de clientes',
            'start_ip'   => '192.168.23.2',
            'end_ip'     => '192.168.23.248',
        ]);
         Pool::create([
            'nodo_id'    =>6, // Nodo epon sabana
            'nombre'     => 'clientes-pool',
            'descripcion'=> 'Pool principal para Onus de clientes',
            'start_ip'   => '10.60.22.2',
            'end_ip'     => '10.60.22.248',
        ]);
                 
    }
}
