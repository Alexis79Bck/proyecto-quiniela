<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\{Model, Collection};

abstract class BaseEloquentRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }
}
