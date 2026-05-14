<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {

            Factura::create([
                'cliente_id' => $cliente->id,
                'total' => 50000,
                'estado' => 'pendiente',
                'fecha' => now(),
            ]);

        }
    }
}
