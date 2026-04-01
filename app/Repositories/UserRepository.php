<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $modelClass = User::class;

    protected $searchable = [
        'name',
        'email',
        'username',
    ];

    protected $with = [
        'role.permissions',
    ];

    public function findByLogin($login)
    {
        return $this->query()
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('username', $login);
            })
            ->first();
    }
}
