<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $modelClass;

    protected $searchable = [];

    protected $with = [];

    public function query()
    {
        $model = $this->makeModel();

        return $model->newQuery();
    }

    public function all(array $with = [])
    {
        return $this->query()->with($with ?: $this->with)->get();
    }

    public function paginate($perPage = 15, $search = null, array $with = [], array $order = ['id', 'desc'], array $withCount = [])
    {
        $query = $this->query()->with($with ?: $this->with);

        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        if ($search !== null && trim($search) !== '') {
            $this->applySearch($query, $search);
        }

        if (!empty($order)) {
            $column = isset($order[0]) ? $order[0] : 'id';
            $direction = isset($order[1]) ? $order[1] : 'desc';
            $query->orderBy($column, $direction);
        }

        return $query->paginate($perPage)->appends(request()->query());
    }

    public function find($id, array $with = [])
    {
        return $this->query()->with($with ?: $this->with)->find($id);
    }

    public function findOrFail($id, array $with = [])
    {
        return $this->query()->with($with ?: $this->with)->findOrFail($id);
    }

    public function create(array $data)
    {
        $class = $this->modelClass;

        return $class::create($data);
    }

    public function update(Model $model, array $data)
    {
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

    protected function applySearch(Builder $query, $search)
    {
        if (empty($this->searchable)) {
            return $query;
        }

        $search = trim($search);

        $query->where(function ($inner) use ($search) {
            foreach ($this->searchable as $field) {
                $inner->orWhere($field, 'like', '%' . $search . '%');
            }
        });

        return $query;
    }

    protected function makeModel()
    {
        $class = $this->modelClass;

        return new $class;
    }
}
