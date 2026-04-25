<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AuditLogRepository extends BaseEloquentRepository implements AuditLogRepositoryInterface
{
    public function __construct(AuditLog $model)
    {
        parent::__construct($model);
    }

    public function createLog(array $data): AuditLog
    {
        return $this->create($data);
    }

    public function find(int $id): ?AuditLog
    {
        return parent::find($id);
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }
}
