<?php

namespace App\Repositories\Eloquent;

use App\Models\Equipo;
use App\Repositories\Contracts\EquipoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EquipoRepository extends BaseEloquentRepository implements EquipoRepositoryInterface
{
    public function __construct(Equipo $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Equipo
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Equipo
    {
        return parent::find($id);
    }

    public function findByName(string $name): ?Equipo
    {
        return $this->model->where('nombre', $name)->first();
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }

    public function update(int $id, array $attributes): bool
    {
        $equipo = $this->find($id);
        if ($equipo) {
            return $equipo->update($attributes);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $equipo = $this->find($id);
        if ($equipo) {
            return $equipo->delete();
        }

        return false;
    }

    public function getByGroup(string $group): Collection
    {
        return $this->model->where('grupo', $group)->get();
    }
}