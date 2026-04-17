<?php

namespace App\Infrastructure\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class PusherChannel
{
    /**
     * The Pusher instance.
     */
    protected Pusher $pusher;

    /**
     * Create a new channel instance.
     */
    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        try {
            // Obtener datos de la notificación
            $data = $notification->toBroadcast($notifiable);
            
            // Determinar canales y evento
            $channels = $this->getChannels($notifiable, $notification);
            $event = $this->getEventName($notification);

            $this->pusher->trigger(
                $channels,
                $event,
                $data
            );
        } catch (\Pusher\PusherException $e) {
            // Errores de Pusher (red, timeout, etc.) - registrar pero no reintentar
            Log::error('Error sending Pusher notification', [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            // No relanzar para evitar reintentos innecesarios
        } catch (\Exception $e) {
            // Otros errores - registrar pero no reintentar
            Log::error('Unexpected error sending Pusher notification', [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // No relanzar para evitar reintentos innecesarios
        }
    }

    /**
     * Get the channels the notification should be broadcast on.
     *
     * @return array
     */
    protected function getChannels($notifiable, Notification $notification): array
     {
         // Si la notificación tiene método broadcastOn, usarlo
         if (method_exists($notification, 'broadcastOn')) {
             $channels = $notification->broadcastOn($notifiable);
             return array_map(function ($channel) {
                 return $channel->name ?? $channel;
             }, $channels);
         }
         
         // Canal por defecto basado en el tipo de notifiable
         return ['notifications'];
     }

    /**
     * Get the event name for the notification.
     *
     * @return string
     */
    protected function getEventName(Notification $notification): string
    {
        // Si la notificación tiene método broadcastAs, usarlo
        if (method_exists($notification, 'broadcastAs')) {
            return $notification->broadcastAs();
        }
        
        // Nombre del evento basado en el nombre de la clase
        return class_basename($notification);
    }
}
