<?php

namespace App\Services;

use App\Models\Direction;
use App\Repositories\DirectionRepository;

class DirectionService
{
    protected $directions;

    protected $activityLogs;

    public function __construct(DirectionRepository $directions, ActivityLogService $activityLogs)
    {
        $this->directions = $directions;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->directions->paginate(15, $search, ['stocks.plant'], ['name', 'asc']);
    }

    public function find($id)
    {
        return $this->directions->findOrFail($id, ['stocks.plant']);
    }

    public function create(array $data)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
        $direction = $this->directions->create($data);

        $this->activityLogs->log('directions', 'create', 'Se creó la dirección ' . $direction->name, $direction->id, null, $direction->toArray());

        return $direction;
    }

    public function update(Direction $direction, array $data)
    {
        $oldValues = $direction->toArray();
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $direction = $this->directions->update($direction, $data);

        $this->activityLogs->log('directions', 'update', 'Se actualizó la dirección ' . $direction->name, $direction->id, $oldValues, $direction->toArray());

        return $direction;
    }

    public function delete(Direction $direction)
    {
        $oldValues = $direction->toArray();
        $this->directions->delete($direction);

        $this->activityLogs->log('directions', 'delete', 'Se eliminó la dirección ' . $oldValues['name'], $oldValues['id'], $oldValues, null);

        return true;
    }
}
