<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseEloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseEloquentRepository implements BaseEloquentRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes)
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

    public function allWith(array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->get($columns);
    }
}
