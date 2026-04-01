<?php

namespace App\Repositories;

use App\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    protected $modelClass = ActivityLog::class;

    protected $searchable = [
        'module',
        'action',
        'description',
        'ip_address',
    ];

    protected $with = [
        'user.role',
    ];
}
