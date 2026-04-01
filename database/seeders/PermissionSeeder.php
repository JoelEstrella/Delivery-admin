<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'Ver usuarios', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Crear usuarios', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Actualizar usuarios', 'slug' => 'users.update', 'module' => 'users'],
            ['name' => 'Eliminar usuarios', 'slug' => 'users.delete', 'module' => 'users'],

            ['name' => 'Ver roles', 'slug' => 'roles.view', 'module' => 'roles'],
            ['name' => 'Crear roles', 'slug' => 'roles.create', 'module' => 'roles'],
            ['name' => 'Actualizar roles', 'slug' => 'roles.update', 'module' => 'roles'],
            ['name' => 'Eliminar roles', 'slug' => 'roles.delete', 'module' => 'roles'],

            ['name' => 'Ver CCT', 'slug' => 'ccts.view', 'module' => 'ccts'],
            ['name' => 'Crear CCT', 'slug' => 'ccts.create', 'module' => 'ccts'],
            ['name' => 'Actualizar CCT', 'slug' => 'ccts.update', 'module' => 'ccts'],
            ['name' => 'Eliminar CCT', 'slug' => 'ccts.delete', 'module' => 'ccts'],

            ['name' => 'Ver plantas', 'slug' => 'plants.view', 'module' => 'plants'],
            ['name' => 'Crear plantas', 'slug' => 'plants.create', 'module' => 'plants'],
            ['name' => 'Actualizar plantas', 'slug' => 'plants.update', 'module' => 'plants'],
            ['name' => 'Eliminar plantas', 'slug' => 'plants.delete', 'module' => 'plants'],

            ['name' => 'Ver direcciones', 'slug' => 'directions.view', 'module' => 'directions'],
            ['name' => 'Crear direcciones', 'slug' => 'directions.create', 'module' => 'directions'],
            ['name' => 'Actualizar direcciones', 'slug' => 'directions.update', 'module' => 'directions'],
            ['name' => 'Eliminar direcciones', 'slug' => 'directions.delete', 'module' => 'directions'],

            ['name' => 'Ver entregas', 'slug' => 'deliveries.view', 'module' => 'deliveries'],
            ['name' => 'Crear entregas', 'slug' => 'deliveries.create', 'module' => 'deliveries'],
            ['name' => 'Actualizar entregas', 'slug' => 'deliveries.update', 'module' => 'deliveries'],
            ['name' => 'Eliminar entregas', 'slug' => 'deliveries.delete', 'module' => 'deliveries'],

            ['name' => 'Ver validaciones', 'slug' => 'validations.view', 'module' => 'validations'],
            ['name' => 'Crear validaciones', 'slug' => 'validations.create', 'module' => 'validations'],
            ['name' => 'Actualizar validaciones', 'slug' => 'validations.update', 'module' => 'validations'],
            ['name' => 'Eliminar validaciones', 'slug' => 'validations.delete', 'module' => 'validations'],
            ['name' => 'Aprobar validaciones', 'slug' => 'validations.approve', 'module' => 'validations'],

            ['name' => 'Ver bitácora', 'slug' => 'logs.view', 'module' => 'logs'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $permissionIds = Permission::pluck('id', 'slug');
        $allPermissionIds = Permission::pluck('id')->all();

        $rolePermissions = [
            'super-admin' => $allPermissionIds,
            'administrador' => $allPermissionIds,
            'capturista' => [
                $permissionIds['ccts.view'],
                $permissionIds['ccts.create'],
                $permissionIds['ccts.update'],
                $permissionIds['plants.view'],
                $permissionIds['plants.create'],
                $permissionIds['plants.update'],
                $permissionIds['deliveries.view'],
                $permissionIds['deliveries.create'],
                $permissionIds['deliveries.update'],
                $permissionIds['logs.view'],
            ],
            'direccion' => [
                $permissionIds['ccts.view'],
                $permissionIds['plants.view'],
                $permissionIds['directions.view'],
                $permissionIds['directions.create'],
                $permissionIds['directions.update'],
                $permissionIds['deliveries.view'],
                $permissionIds['validations.view'],
                $permissionIds['validations.create'],
                $permissionIds['validations.update'],
                $permissionIds['validations.approve'],
                $permissionIds['logs.view'],
            ],
            'validador' => [
                $permissionIds['deliveries.view'],
                $permissionIds['validations.view'],
                $permissionIds['validations.create'],
                $permissionIds['validations.update'],
                $permissionIds['validations.approve'],
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $ids) {
            $role = Role::where('slug', $roleSlug)->first();

            if ($role) {
                $role->permissions()->sync($ids);
            }
        }
    }
}
