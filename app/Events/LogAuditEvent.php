<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogAuditEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(
        public readonly ?int $userId,
        public readonly string $accion,
        public readonly ?string $tipoEntidad,
        public readonly ?int $entityId,
        public readonly ?array $oldValues,
        public readonly ?array $newValues,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly array $metadata = [],
        public readonly string $logChannel = 'audit'
    ) {}

    /**
     * Obtener los datos del evento como arreglo.
     */
    public function toArray(): array
    {
        return [
            'usuario_id' => $this->userId,
            'accion' => $this->accion,
            'tipo_entidad' => $this->tipoEntidad,
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
