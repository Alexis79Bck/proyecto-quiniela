<?php

namespace App\Repositories\Eloquent;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository extends BaseEloquentRepository implements UsuarioRepositoryInterface
{
    public function __construct(Usuario $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Usuario
    {
        return parent::create($attributes);
    }

    public function find(int $id): ?Usuario
    {
        return parent::find($id);
    }

    public function findByEmail(string $email): ?Usuario
    {
        return $this->model->where('correo_electronico', $email)->first();
    }

    public function all(array $columns = ['*']): Collection
    {
        return parent::all($columns);
    }

    public function update(int $id, array $attributes): bool
    {
        $user = $this->find($id);
        if ($user) {
            return $user->update($attributes);
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if ($user) {
            return $user->delete();
        }

        return false;
    }
}
