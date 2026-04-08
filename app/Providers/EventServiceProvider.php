<?php

namespace App\Providers;

use App\Domain\Auth\Events\LogAuditEvent;
use App\Domain\Auth\Listeners\AuditLogListener;
use App\Domain\Match\Events\MatchResultAvailable;
use App\Domain\Match\Events\MatchStarted;
use App\Domain\Prediction\Events\PredictionReminder;
use App\Domain\Quiniela\Events\NewQuinielaAvailable;
use App\Domain\Quiniela\Events\WinnersAnnounced;
use App\Domain\Scoring\Events\LeaderboardUpdated;
use App\Infrastructure\Notifications\Listeners\SendPusherNotification;
use App\Infrastructure\Toast\Listeners\SendSystemToastNotifications;
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
        NewQuinielaAvailable::class => [
            SendPusherNotification::class,
            SendSystemToastNotifications::class,
        ],
        MatchStarted::class => [
            SendPusherNotification::class,
        ],
        MatchResultAvailable::class => [
            SendPusherNotification::class,
        ],
        LeaderboardUpdated::class => [
            SendPusherNotification::class,
        ],
        PredictionReminder::class => [
            SendPusherNotification::class,
        ],
        WinnersAnnounced::class => [
            SendPusherNotification::class,
            SendSystemToastNotifications::class,
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
}
