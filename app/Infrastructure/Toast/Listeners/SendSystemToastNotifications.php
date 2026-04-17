<?php

namespace App\Infrastructure\Toast\Listeners;

use App\Domain\Quiniela\Events\NewQuinielaAvailable;
use App\Domain\Quiniela\Events\WinnersAnnounced;
use App\Domain\User\Models\User;
use App\Services\ToastService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSystemToastNotifications implements ShouldQueue
{
    public string $queue = 'notifications';

    public function __construct(
        protected ToastService $toastService
    ) {}

    public function handle(object $event): void
    {
        $recipients = $this->getRecipients($event);
        
        foreach ($recipients as $user) {
            $this->sendToastForEvent($event, $user);
        }
    }

    protected function getRecipients(object $event): \Illuminate\Support\Collection
    {
        return match (true) {
            $event instanceof NewQuinielaAvailable => User::all()->pluck('id'),
            $event instanceof WinnersAnnounced => User::all()->pluck('id'),
            default => collect(),
        };
    }

    protected function sendToastForEvent(object $event, int $userId): void
    {
        match (true) {
            $event instanceof NewQuinielaAvailable => $this->toastService->info(
                $userId,
                "Nueva quiniela disponible: {$event->quinielaName}",
                'Nueva Quiniela'
            ),
            $event instanceof WinnersAnnounced => $this->toastService->success(
                $userId,
                "¡Ganadores anunciados en {$event->quinielaName}!",
                'Resultados Disponibles'
            ),
            default => null,
        };
    }
}