<?php

namespace App\Repositories\Contracts;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Collection;

interface JuegoRepositoryInterface
{
    public function create(array $attributes): Juego;

    public function find(int $id): ?Juego;

    public function all(array $columns = ['*']): Collection;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;

    public function getByStage(int $stageId): Collection;

    public function getUpcoming(): Collection;

    public function getByDate(string $date): Collection;

    public function getWithPredictions(int $id): ?Juego;
}