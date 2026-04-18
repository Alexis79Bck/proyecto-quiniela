<?php

namespace App\Listeners;

use App\Events\LogAuditEvent;
use App\Infrastructure\Logging\AuditLogger\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuditLogListener implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     */
    public string $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'audit';

    /**
     * The time (seconds) before the job should be processed.
     */
    public int $delay = 0;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /**
     * Handle the event.
     */
    public function handle(LogAuditEvent $event): void
    {
        // Usar datos del evento directamente en lugar de depender del contexto de request
        // ya que el listener puede ejecutarse de forma asíncrona en la cola
        $this->auditLogger->log(
            $event->action,
            $event->entityType,
            $event->entityId,
            $event->oldValues,
            $event->newValues,
            $event->metadata,
            $event->logChannel,
            $event->userId,
            $event->ipAddress,
            $event->userAgent
        );
    }

    /**
     * Determine if the listener should be queued.
     */
    public function shouldBeQueued(LogAuditEvent $event): bool
    {
        // Always queue audit logs for better performance
        return true;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['audit', 'logging'];
    }
}
