<?php

namespace App\Services\Toast;

use App\Enums\ToastType;
use Illuminate\Support\Facades\Log;

class ToastService
{
    public const DEFAULT_DURATION = 5000;
    public const DEFAULT_DISMISSIBLE = true;

    public function broadcast(int $userId, ToastType|string $type, string $message, ?string $title = null, array $options = []): bool
    {
        if (is_string($type)) {
            $type = ToastType::tryFrom($type) ?? ToastType::INFO;
        }

        $toast = $this->formatToast($type, $message, $title, $options);

        try {
            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );
            $pusher->trigger("user-{$userId}", 'toast', $toast);
            Log::info("Toast broadcast to user {$userId}", ['toast' => $toast]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to broadcast toast to user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    public function formatToast(ToastType $type, string $message, ?string $title = null, array $options = []): array
    {
        return [
            'type' => $type->value,
            'title' => $title ?? $type->getDefaultTitle(),
            'message' => $message,
            'icon' => $type->getIcon(),
            'color' => $type->getColor(),
            'duration' => $options['duration'] ?? self::DEFAULT_DURATION,
            'dismissible' => $options['dismissible'] ?? self::DEFAULT_DISMISSIBLE,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function getTypes(): array
    {
        return array_map(fn(ToastType $type) => [
            'type' => $type->value,
            'icon' => $type->getIcon(),
            'color' => $type->getColor(),
            'default_title' => $type->getDefaultTitle(),
        ], ToastType::cases());
    }

    public function success(int $userId, string $message, ?string $title = null, array $options = []): bool
    {
        return $this->broadcast($userId, ToastType::SUCCESS, $message, $title, $options);
    }

    public function error(int $userId, string $message, ?string $title = null, array $options = []): bool
    {
        return $this->broadcast($userId, ToastType::ERROR, $message, $title, $options);
    }

    public function warning(int $userId, string $message, ?string $title = null, array $options = []): bool
    {
        return $this->broadcast($userId, ToastType::WARNING, $message, $title, $options);
    }

    public function info(int $userId, string $message, ?string $title = null, array $options = []): bool
    {
        return $this->broadcast($userId, ToastType::INFO, $message, $title, $options);
    }
}