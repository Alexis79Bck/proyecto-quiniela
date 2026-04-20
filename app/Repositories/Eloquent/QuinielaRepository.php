<?php

namespace App\Repositories\Eloquent;

use App\Models\Quiniela;
use App\Repositories\Contracts\QuinielaRepositoryInterface;

class QuinielaRepository extends BaseEloquentRepository implements QuinielaRepositoryInterface
{
    public function __construct(Quiniela $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Quiniela
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Quiniela
    {
        return parent::find($id);
    }

    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection
    {
        return parent::all($columns);
    }

    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function update(int $id, array $attributes): bool
    {
        $quiniela = $this->find($id);
        if ($quiniela) {
            return $quiniela->update($attributes);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $quiniela = $this->find($id);
        if ($quiniela) {
            return $quiniela->delete();
        }
        return false;
    }
}
