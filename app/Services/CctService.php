<?php

namespace App\Services;

use App\Models\Cct;
use App\Repositories\CctRepository;
use Carbon\Carbon;

class CctService
{
    protected $ccts;

    protected $activityLogs;

    public function __construct(CctRepository $ccts, ActivityLogService $activityLogs)
    {
        $this->ccts = $ccts;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->ccts->paginate(15, $search, [], ['CLAVECCT', 'asc']);
    }

    public function find($id)
    {
        return $this->ccts->findOrFail($id);
    }

    public function create(array $data)
    {
        $data = $this->normalize($data);
        $cct = $this->ccts->create($data);

        $this->activityLogs->log('ccts', 'create', 'Se creó el CCT ' . $cct->CLAVECCT, $cct->id, null, $cct->toArray());

        return $cct;
    }

    public function update(Cct $cct, array $data)
    {
        $oldValues = $cct->toArray();
        $data = $this->normalize($data);
        $cct = $this->ccts->update($cct, $data);

        $this->activityLogs->log('ccts', 'update', 'Se actualizó el CCT ' . $cct->CLAVECCT, $cct->id, $oldValues, $cct->toArray());

        return $cct;
    }

    public function delete(Cct $cct)
    {
        $oldValues = $cct->toArray();
        $this->ccts->delete($cct);

        $this->activityLogs->log('ccts', 'delete', 'Se eliminó el CCT ' . $oldValues['CLAVECCT'], $oldValues['id'], $oldValues, null);

        return true;
    }

    protected function normalize(array $data)
    {
        $dateFields = ['FECHAALTA', 'FECHACLAUS', 'FECHAACTUA'];

        foreach ($dateFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d');
            } else {
                $data[$field] = null;
            }
        }

        if (isset($data['CLAVECCT'])) {
            $data['CLAVECCT'] = strtoupper(trim($data['CLAVECCT']));
        }

        return $data;
    }
}
