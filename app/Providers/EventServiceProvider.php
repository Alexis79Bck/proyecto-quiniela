<?php

namespace App\Providers;

use App\Events\LogAuditEvent;
use App\Listeners\AuditLogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        LogAuditEvent::class => [
            AuditLogListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    /**
     * Skip audit logging in testing environment to prevent memory issues.
     */
    public function subscribe($events): void
    {
        if (app()->environment('testing')) {
            return;
        }

        parent::subscribe($events);
    }
}
