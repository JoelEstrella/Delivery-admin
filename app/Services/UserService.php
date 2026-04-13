<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $users;

    protected $activityLogs;

    public function __construct(UserRepository $users, ActivityLogService $activityLogs)
    {
        $this->users = $users;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->users->paginate(15, $search, ['role.permissions'], ['name', 'asc']);
    }

    public function find($id)
    {
        return $this->users->findOrFail($id, ['role.permissions']);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;

        $user = $this->users->create($data);

        $this->activityLogs->log('users', 'create', 'Se creó el usuario ' . $user->name, $user->id, null, $this->cleanValues($user->toArray()));

        return $user->load('role');
    }

    public function update(User $user, array $data)
    {
        $oldValues = $this->cleanValues($user->toArray());

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        $user = $this->users->update($user, $data);

        $this->activityLogs->log('users', 'update', 'Se actualizó el usuario ' . $user->name, $user->id, $oldValues, $this->cleanValues($user->toArray()));

        return $user;
    }

    public function delete(User $user)
    {
        $oldValues = $this->cleanValues($user->toArray());

        $user->is_active = false;
        $user->save();
        $user->delete();

        $this->activityLogs->log('users', 'delete', 'Se eliminó el usuario ' . $oldValues['name'], $oldValues['id'], $oldValues, null);

        return true;
    }

    protected function cleanValues(array $values)
    {
        unset($values['password'], $values['remember_token']);

        return $values;
    }
}
