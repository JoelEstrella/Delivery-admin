<?php

namespace Database\Seeders;

use App\Models\Direction;
use Illuminate\Database\Seeder;

class DirectionSeeder extends Seeder
{
    public function run()
    {
        Direction::updateOrCreate(
            ['code' => 'DG-001'],
            [
                'name' => 'Dirección General',
                'responsible_name' => 'Administrador del Sistema',
                'phone' => '9990000000',
                'email' => 'direccion.general@example.com',
                'address' => 'Oficina central del sistema administrativo',
                'is_active' => true,
            ]
        );
    }
}
