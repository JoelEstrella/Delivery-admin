<?php

namespace App\Repositories;

use App\Models\StockMovement;

class StockMovementRepository extends BaseRepository
{
    protected $modelClass = StockMovement::class;

    protected $with = [
        'direction',
        'plant',
        'creator.role',
    ];
}
