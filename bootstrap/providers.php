<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\RepositoryServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    RepositoryServiceProvider::class,
    SanctumServiceProvider::class,
    FortifyServiceProvider::class,
];
