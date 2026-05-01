<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseEloquentRepositoryInterface
{
    public function create(array $attributes);

    public function find(int $id): ?Model;

    public function all(array $columns = ['*']): Collection;

    public function allWith(array $columns = ['*'], array $relations = []): Collection;
}
