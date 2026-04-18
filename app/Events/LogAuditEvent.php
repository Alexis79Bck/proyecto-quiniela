<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogAuditEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly ?int $userId,
        public readonly string $action,
        public readonly ?string $entityType,
        public readonly ?int $entityId,
        public readonly ?array $oldValues,
        public readonly ?array $newValues,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly array $metadata = [],
        public readonly string $logChannel = 'audit'
    ) {}

    /**
     * Get the event data as array.
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'action' => $this->action,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'old_values' => $this->oldValues,
            'new_values' => $this->newValues,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'metadata' => $this->metadata,
            'log_channel' => $this->logChannel,
        ];
    }
}
