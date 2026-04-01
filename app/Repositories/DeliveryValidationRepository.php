<?php

namespace App\Repositories;

use App\Models\DeliveryValidation;

class DeliveryValidationRepository extends BaseRepository
{
    protected $modelClass = DeliveryValidation::class;

    protected $searchable = [
        'status',
        'observations',
    ];

    protected $with = [
        'delivery.cct',
        'delivery.direction',
        'validator.role',
    ];

    protected function applySearch($query, $search)
    {
        $search = trim($search);

        $query->where(function ($inner) use ($search) {
            $inner->where('status', 'like', '%' . $search . '%')
                ->orWhere('observations', 'like', '%' . $search . '%')
                ->orWhereHas('delivery', function ($delivery) use ($search) {
                    $delivery->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('cct', function ($cct) use ($search) {
                            $cct->where('CLAVECCT', 'like', '%' . $search . '%')
                                ->orWhere('NOMBRECT', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('direction', function ($direction) use ($search) {
                            $direction->where('name', 'like', '%' . $search . '%')
                                ->orWhere('code', 'like', '%' . $search . '%');
                        });
                });
        });

        return $query;
    }
}
