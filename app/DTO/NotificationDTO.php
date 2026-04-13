<?php

namespace App\DTO;

use Illuminate\Support\Carbon;

class NotificationDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $title,
        public readonly string $message,
        public readonly array $data,
        public readonly ?Carbon $readAt,
        public readonly Carbon $createdAt,
    ) {}

    /**
     * Create a NotificationDTO from a notification model.
     *
     * @param mixed $notification The notification model instance
     * @return self
     */
    public static function fromModel($notification): self
    {
        return new self(
            id: $notification->id,
            type: $notification->type,
            title: self::extractTitle($notification),
            message: self::extractMessage($notification),
            data: $notification->data ?? [],
            readAt: $notification->read_at ? Carbon::parse($notification->read_at) : null,
            createdAt: Carbon::parse($notification->created_at),
        );
    }

    /**
     * Extract title from notification data.
     *
     * @param mixed $notification
     * @return string
     */
    private static function extractTitle($notification): string
    {
        // Default titles based on notification type
        $titles = [
            'App\\Infrastructure\\Notifications\\NewQuinielaNotification' => 'Nueva Quiniela Disponible',
            'App\\Infrastructure\\Notifications\\WinnersNotification' => 'Ganadores Anunciados',
            'App\\Infrastructure\\Notifications\\PredictionReminderNotification' => 'Recordatorio de Predicción',
            'App\\Infrastructure\\Notifications\\MatchStartedNotification' => 'Partido Comenzado',
            'App\\Infrastructure\\Notifications\\MatchResultNotification' => 'Resultado del Partido',
            'App\\Infrastructure\\Notifications\\LeaderboardUpdateNotification' => 'Actualización de Tabla',
        ];

        // Try to get from notification data first
        if (isset($notification->data['title'])) {
            return $notification->data['title'];
        }

        // Fallback to default title based on type
        return $titles[$notification->type] ?? 'Notificación';
    }

    /**
     * Extract message from notification data.
     *
     * @param mixed $notification
     * @return string
     */
    private static function extractMessage($notification): string
    {
        // Try to get from notification data first
        if (isset($notification->data['message'])) {
            return $notification->data['message'];
        }

        // Provide generic messages based on type if not in data
        $messages = [
            'App\\Infrastructure\\Notifications\\NewQuinielaNotification' => 'Hay una nueva quiniela disponible para unirse.',
            'App\\Infrastructure\\Notifications\\WinnersNotification' => 'Se han anunciado los ganadores de una quiniela.',
            'App\\Infrastructure\\Notifications\\PredictionReminderNotification' => 'No olvides hacer tu predicción antes de que inicie el partido.',
            'App\\Infrastructure\\Notifications\\MatchStartedNotification' => 'Un partido ha comenzado. ¡Ve los resultados en vivo!',
            'App\\Infrastructure\\Notifications\\MatchResultNotification' => 'Un partido ha terminado y hay nuevos resultados disponibles.',
            'App\\Infrastructure\\Notifications\\LeaderboardUpdateNotification' => 'La tabla de posiciones se ha actualizado.',
        ];

        return $messages[$notification->type] ?? 'Tienes una nueva notificación.';
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
            'read_at' => $this->readAt?->toISOString(),
            'created_at' => $this->createdAt->toISOString(),
        ];
    }
}