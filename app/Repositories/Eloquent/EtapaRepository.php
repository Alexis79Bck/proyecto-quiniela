<?php

namespace App\Repositories\Eloquent;

use App\Models\Etapa;
use App\Repositories\Contracts\EtapaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EtapaRepository extends BaseEloquentRepository implements EtapaRepositoryInterface
{
    public function __construct(Etapa $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Etapa
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Etapa
    {
        return parent::find($id);
    }

    public function findByName(string $name): ?Etapa
    {
        return $this->model->where('nombre', $name)->first();
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }

    public function update(int $id, array $attributes): bool
    {
        $etapa = $this->find($id);
        if ($etapa) {
            return $etapa->update($attributes);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $etapa = $this->find($id);
        if ($etapa) {
            return $etapa->delete();
        }

        return false;
    }
}