<?php

namespace App\Infrastructure\Notifications\Listeners;

use App\Domain\Match\Events\MatchResultAvailable;
use App\Domain\Match\Events\MatchStarted;
use App\Domain\Prediction\Events\PredictionReminder;
use App\Domain\Quiniela\Events\NewQuinielaAvailable;
use App\Domain\Quiniela\Events\WinnersAnnounced;
use App\Domain\Scoring\Events\LeaderboardUpdated;
use App\Models\User;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendPusherNotification implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     */
    public string $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'notifications';

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public array $backoff = [60, 120, 240];

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Determinar qué notificación enviar según el tipo de evento
        $notification = match (true) {
            $event instanceof NewQuinielaAvailable => new \App\Infrastructure\Notifications\NewQuinielaNotification(
                $event->quinielaId,
                $event->quinielaName,
                $event->description,
                $event->startDate,
                $event->endDate
            ),
            $event instanceof MatchStarted => new \App\Infrastructure\Notifications\MatchStartedNotification(
                $event->matchId,
                $event->homeTeam,
                $event->awayTeam,
                $event->startTime,
                $event->stage
            ),
            $event instanceof MatchResultAvailable => new \App\Infrastructure\Notifications\MatchResultNotification(
                $event->matchId,
                $event->homeTeam,
                $event->awayTeam,
                $event->homeScore,
                $event->awayScore,
                $event->stage
            ),
            $event instanceof LeaderboardUpdated => new \App\Infrastructure\Notifications\LeaderboardUpdateNotification(
                $event->quinielaId,
                $event->quinielaName,
                $event->topUsers,
                $event->totalParticipants
            ),
            $event instanceof PredictionReminder => new \App\Infrastructure\Notifications\PredictionReminderNotification(
                $event->userId,
                $event->matchId,
                $event->homeTeam,
                $event->awayTeam,
                $event->matchTime,
                $event->quinielaName
            ),
            $event instanceof WinnersAnnounced => new \App\Infrastructure\Notifications\WinnersNotification(
                $event->quinielaId,
                $event->quinielaName,
                $event->winners,
                $event->prizeDescription
            ),
            default => null,
        };

        if ($notification) {
            // Determinar destinatarios según el tipo de evento
            $this->sendNotificationToRecipients($event, $notification);
        }
    }

    /**
     * Enviar notificación a los destinatarios de manera eficiente.
     */
    protected function sendNotificationToRecipients(object $event, $notification): void
    {
        // Para eventos que requieren todos los usuarios, usar chunk para eficiencia
        if ($this->requiresAllUsers($event)) {
            User::chunk(100, function ($users) use ($notification) {
                Notification::send($users, $notification);
            });
            return;
        }

        // Para eventos con destinatarios específicos, usar LazyCollection para eficiencia
        $recipients = $this->getRecipients($event);
        
        if ($recipients->isNotEmpty()) {
            // Procesar en chunks de 100 usuarios
            $recipients->chunk(100)->each(function ($chunk) use ($notification) {
                Notification::send($chunk, $notification);
            });
        }
    }

    /**
     * Determinar si el evento requiere notificar a todos los usuarios.
     */
    protected function requiresAllUsers(object $event): bool
    {
        return $event instanceof NewQuinielaAvailable
            || $event instanceof MatchStarted
            || $event instanceof MatchResultAvailable;
    }

    /**
     * Determinar los destinatarios de la notificación según el tipo de evento.
     * Solo para eventos que no requieren todos los usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecipients(object $event): \Illuminate\Support\LazyCollection
    {
        $maxLimit = 10000;
        
        return match (true) {
            // Recordatorio de predicción: solo al usuario específico
            $event instanceof PredictionReminder => 
                User::where('id', $event->userId)->lazy(100),
            
            // Actualización de leaderboard: solo usuarios de la quiniela
            // TODO: Requiere implementar relación quinielas() en modelo User
            $event instanceof LeaderboardUpdated => 
                User::whereHas('quinielas', function ($query) use ($event) {
                    $query->where('quiniela_id', $event->quinielaId);
                })->limit($maxLimit)->lazy(100),
            
            // Ganadores anunciados: solo usuarios de la quiniela
            // TODO: Requiere implementar relación quinielas() en modelo User
            $event instanceof WinnersAnnounced => 
                User::whereHas('quinielas', function ($query) use ($event) {
                    $query->where('quiniela_id', $event->quinielaId);
                })->limit($maxLimit)->lazy(100),
            
            // Por defecto: LazyCollection vacía (no debería llegar aquí)
            default => collect()->lazy(),
        };
    }

}
