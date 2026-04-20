<?php

namespace App\Repositories\Contracts;

use App\Models\Quiniela;

interface QuinielaRepositoryInterface
{
    public function create(array $attributes): Quiniela;
    public function find(int $id): ?Quiniela;
    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;
    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection;
    public function findActive(): \Illuminate\Database\Eloquent\Collection;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
}
