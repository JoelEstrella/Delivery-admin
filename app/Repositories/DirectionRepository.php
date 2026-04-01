<?php

namespace App\Repositories;

use App\Models\Direction;

class DirectionRepository extends BaseRepository
{
    protected $modelClass = Direction::class;

    protected $searchable = [
        'name',
        'code',
        'responsible_name',
        'phone',
        'email',
    ];

    protected $with = [
        'stocks.plant',
    ];
}
