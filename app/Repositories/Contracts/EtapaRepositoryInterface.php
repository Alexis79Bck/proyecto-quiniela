<?php

namespace App\Repositories\Contracts;

use App\Models\Etapa;
use Illuminate\Database\Eloquent\Collection;

interface EtapaRepositoryInterface
{
    public function create(array $attributes): Etapa;

    public function find(int $id): ?Etapa;

    public function findByName(string $name): ?Etapa;

    public function all(array $columns = ['*']): Collection;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}