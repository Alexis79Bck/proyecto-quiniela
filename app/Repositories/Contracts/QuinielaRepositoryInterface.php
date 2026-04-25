<?php

namespace App\Repositories\Contracts;

use App\Models\Quiniela;
use Illuminate\Database\Eloquent\Collection;

interface QuinielaRepositoryInterface
{
    public function create(array $attributes): Quiniela;

    public function find(int $id): ?Quiniela;

    public function all(array $columns = ['*']): Collection;

    public function findByUser(int $userId): Collection;

    public function findActive(): Collection;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
