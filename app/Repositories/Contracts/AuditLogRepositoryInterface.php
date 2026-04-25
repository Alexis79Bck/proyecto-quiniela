<?php

namespace App\Repositories\Contracts;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Collection;

interface AuditLogRepositoryInterface
{
    public function createLog(array $data): AuditLog;

    public function find(int $id): ?AuditLog;

    public function all(array $columns = ['*']): Collection;
}
