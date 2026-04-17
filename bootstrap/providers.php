<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\FortifyServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    SanctumServiceProvider::class,
    FortifyServiceProvider::class,
];
