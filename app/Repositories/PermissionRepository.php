<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    protected $modelClass = Permission::class;

    protected $searchable = [
        'name',
        'slug',
        'module',
        'description',
    ];
}
