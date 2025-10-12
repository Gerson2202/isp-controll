<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 🔄 Limpia la caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🧱 Lista de permisos personalizados
        $permissions = [
            'gestionar usuarios',
            'gestionar roles',
            'crear clientes',
            'ver clientes',
            'editar informacion de cliente',
            'ver imagenes del cliente',
            'modificar estado de cliente',
            'modificar plan de cliente',
            'modificar nodo de cliente',
            'crear contrato',
            'ver lista de contrato',
            'editar contrato',
            'ver dashborad financiero',
            'crear facturas',
            'ver historico de facturas',
            'registrar pagos',
            'asignar ip',
            'crear pool',
            'cortar clientes masivos',
            'gestionar planes',
            'agregar modelo de equipo',
            'agregar equipo',
            'ver equipos',
            'editar tickets',
            'ver historial de tickets',
            'ver calendario',
            'editar programacion', 
            'ver programacion',
            'gestionar nodos',
            'ver monitoreo de nodos',
        ];

        // 🔹 Crear permisos si no existen
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 🔹 Crear roles base
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $soporte = Role::firstOrCreate(['name' => 'soporte']);
        $financiero = Role::firstOrCreate(['name' => 'financiero']);

        // 🔹 Asignar permisos a los roles
        $admin->givePermissionTo(Permission::all());

        $soporte->givePermissionTo([
            'ver clientes',
            'editar informacion de cliente',
            'ver imagenes del cliente',
            'modificar estado de cliente',
            'modificar plan de cliente',
            'modificar nodo de cliente',
            'asignar ip',
            'crear pool',
            'cortar clientes masivos',
            'gestionar planes',
            'agregar modelo de equipo',
            'agregar equipo',
            'ver equipos',
            'editar tickets',
            'ver historial de tickets',
            'editar programacion',
            'ver programacion',
            'ver monitoreo de nodos',
        ]);

        $financiero->givePermissionTo([
            'crear clientes',
            'ver clientes',
            'ver dashborad financiero',
            'crear facturas',
            'registrar pagos',
            'ver historico de facturas',
            'crear contrato',
            'ver lista de contrato',
            'editar contrato',
        ]);

        

        
        // 5️⃣ Asignar roles a usuarios existentes
        $user1 = User::where('email', 'gersonpsj@gmail.com')->first();
        if ($user1) {
            $user1->assignRole('admin');
        }

        $user2 = User::where('email', 'admin@gmail.com')->first();
        if ($user2) {
            $user2->assignRole('admin');
        }

        $user3 = User::where('email', 'Soporte@gmail.com')->first();
        if ($user3) {
            $user3->assignRole('soporte');
        }
        

        $this->command->info('✅ Roles y permisos creados y asignados correctamente.');
    }
}
