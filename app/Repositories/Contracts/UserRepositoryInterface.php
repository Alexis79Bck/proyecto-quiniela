<?php

namespace App\Repositories\Contracts;

use App\Models\Usuario;

interface UserRepositoryInterface
{
    public function create(array $attributes): Usuario;
    public function find(int $id): ?Usuario;
    public function findByEmail(string $email): ?Usuario;
    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
}
