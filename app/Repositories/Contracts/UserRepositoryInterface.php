<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $attributes): User;
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
}
