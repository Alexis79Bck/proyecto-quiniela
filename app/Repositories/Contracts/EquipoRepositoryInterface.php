<?php

namespace App\Repositories\Contracts;

use App\Models\Equipo;
use Illuminate\Database\Eloquent\Collection;

interface EquipoRepositoryInterface
{
    public function create(array $attributes): Equipo;

    public function find(int $id): ?Equipo;

    public function findByName(string $name): ?Equipo;

    public function all(array $columns = ['*']): Collection;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;

    public function getByGroup(string $group): Collection;
}