<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $role = Role::where('slug', 'super-admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'role_id' => $role ? $role->id : null,
                'name' => 'Administrador',
                'username' => 'admin',
                'password' => Hash::make('Admin12345*'),
                'is_active' => true,
            ]
        );
    }
}
