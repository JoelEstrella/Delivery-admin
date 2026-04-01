<?php

namespace App\Repositories;

use App\Models\Delivery;

class DeliveryRepository extends BaseRepository
{
    protected $modelClass = Delivery::class;

    protected $searchable = [
        'status',
        'delivered_by',
        'observations',
    ];

    protected $with = [
        'cct',
        'direction',
        'creator.role',
        'items.plant',
        'validation.validator.role',
    ];

    protected function applySearch($query, $search)
    {
        $search = trim($search);

        $query->where(function ($inner) use ($search) {
            $inner->where('status', 'like', '%' . $search . '%')
                ->orWhere('delivered_by', 'like', '%' . $search . '%')
                ->orWhere('observations', 'like', '%' . $search . '%')
                ->orWhereHas('cct', function ($cct) use ($search) {
                    $cct->where('CLAVECCT', 'like', '%' . $search . '%')
                        ->orWhere('NOMBRECT', 'like', '%' . $search . '%');
                })
                ->orWhereHas('direction', function ($direction) use ($search) {
                    $direction->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
        });

        return $query;
    }
}
