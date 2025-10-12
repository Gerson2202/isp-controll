<?php

namespace Database\Seeders;

use App\Models\Contrato;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Llamamos al seeder de planes
        $this->call([
            
            // PlanSeeder::class,  // Asegúrate de que este seeder esté siendo llamado
            UserSeeder::class,  // Llamamos al seeder de usuarios
            // NodoSeeder::class,
              ClienteSeeder::class,
              RolePermissionSeeder::class,
            // PoolSeeder::class,
            //  ContratoSeeder::class
        ]);
    }
}
