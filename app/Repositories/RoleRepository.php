<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    protected $modelClass = Role::class;

    protected $searchable = [
        'name',
        'slug',
        'description',
    ];

    protected $with = [
        'permissions',
    ];
}
