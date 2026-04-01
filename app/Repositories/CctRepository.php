<?php

namespace App\Repositories;

use App\Models\Cct;

class CctRepository extends BaseRepository
{
    protected $modelClass = Cct::class;

    protected $searchable = [
        'CLAVECCT',
        'NOMBRECT',
        'LOCALIDAD',
        'MUNICIPIO',
        'N_LOCALIDAD',
        'N_MUNICIPIO',
    ];

    public function findByClave($clave)
    {
        return $this->query()->where('CLAVECCT', $clave)->first();
    }
}
