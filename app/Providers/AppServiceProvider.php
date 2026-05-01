<?php

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Eloquent\AuditLogRepository;
use App\Repositories\Eloquent\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuditLogRepositoryInterface::class,
            AuditLogRepository::class
        );

        $this->app->bind(
            UsuarioRepositoryInterface::class,
            UsuarioRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
