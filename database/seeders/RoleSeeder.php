<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Acceso total al sistema.', 'is_active' => true],
            ['name' => 'Administrador', 'slug' => 'administrador', 'description' => 'Administración general del sistema.', 'is_active' => true],
            ['name' => 'Dirección', 'slug' => 'direccion', 'description' => 'Operación y seguimiento de entregas.', 'is_active' => true],
            ['name' => 'Planeación', 'slug' => 'validador', 'description' => 'Validación de entregas recibidas.', 'is_active' => true],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
