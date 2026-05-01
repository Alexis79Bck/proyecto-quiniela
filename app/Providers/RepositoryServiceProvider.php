<?php

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\EquipoRepositoryInterface;
use App\Repositories\Contracts\EtapaRepositoryInterface;
use App\Repositories\Contracts\JuegoRepositoryInterface;
use App\Repositories\Contracts\PrediccionRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;
use App\Repositories\Eloquent\EquipoRepository;
use App\Repositories\Eloquent\EtapaRepository;
use App\Repositories\Eloquent\JuegoRepository;
use App\Repositories\Eloquent\PrediccionRepository;
use App\Repositories\Eloquent\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(EquipoRepositoryInterface::class, EquipoRepository::class);
        $this->app->bind(EtapaRepositoryInterface::class, EtapaRepository::class);
        $this->app->bind(JuegoRepositoryInterface::class, JuegoRepository::class);
        $this->app->bind(PrediccionRepositoryInterface::class, PrediccionRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}