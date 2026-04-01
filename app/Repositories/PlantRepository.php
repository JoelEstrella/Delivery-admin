<?php

namespace App\Repositories;

use App\Models\Plant;

class PlantRepository extends BaseRepository
{
    protected $modelClass = Plant::class;

    protected $searchable = [
        'name',
        'slug',
        'short_description',
    ];

    protected $with = [
        'images',
    ];
}
