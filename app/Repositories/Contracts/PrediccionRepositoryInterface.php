<?php

namespace App\Repositories\Contracts;

use App\Models\Prediccion;
use Illuminate\Database\Eloquent\Collection;

interface PrediccionRepositoryInterface
{
    public function create(array $attributes): Prediccion;

    public function find(int $id): ?Prediccion;

    public function all(array $columns = ['*']): Collection;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;

    public function getByUser(int $userId): Collection;

    public function getByMatch(int $matchId): Collection;

    public function getByUserAndMatch(int $userId, int $matchId): ?Prediccion;

    public function calculatePoints(int $predictionId): int;
}