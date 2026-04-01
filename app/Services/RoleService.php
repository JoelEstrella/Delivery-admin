<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Support\Str;

class RoleService
{
    protected $roles;

    protected $activityLogs;

    public function __construct(RoleRepository $roles, ActivityLogService $activityLogs)
    {
        $this->roles = $roles;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->roles->paginate(15, $search, ['permissions'], ['name', 'asc'], ['permissions', 'users']);
    }

    public function find($id)
    {
        return $this->roles->findOrFail($id, ['permissions']);
    }

    public function create(array $data)
    {
        $permissions = isset($data['permissions']) ? $data['permissions'] : [];
        unset($data['permissions']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

        $role = $this->roles->create($data);
        $role->permissions()->sync($permissions);

        $this->activityLogs->log('roles', 'create', 'Se creó el rol ' . $role->name, $role->id, null, $role->toArray());

        return $role->load('permissions');
    }

    public function update(Role $role, array $data)
    {
        $oldValues = $role->toArray();
        $permissions = isset($data['permissions']) ? $data['permissions'] : [];
        unset($data['permissions']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        $role = $this->roles->update($role, $data);
        $role->permissions()->sync($permissions);

        $this->activityLogs->log('roles', 'update', 'Se actualizó el rol ' . $role->name, $role->id, $oldValues, $role->toArray());

        return $role->load('permissions');
    }

    public function deactivate(Role $role)
    {
        $oldValues = $role->toArray();
        $role->is_active = false;
        $role->save();

        $this->activityLogs->log('roles', 'delete', 'Se desactivó el rol ' . $role->name, $role->id, $oldValues, $role->toArray());

        return $role;
    }
}
