<?php

namespace App\Repositories\Contracts;

use App\Models\AuditLog;

interface AuditLogRepositoryInterface
{
    public function createLog(array $data): AuditLog;
    public function find(int $id): ?AuditLog;
    public function all(array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;
}
