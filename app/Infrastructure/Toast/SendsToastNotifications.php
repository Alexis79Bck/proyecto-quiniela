<?php

namespace App\Infrastructure\Toast;

use App\Services\Toast\ToastService;

trait SendsToastNotifications
{
    protected ToastService $toastService;

    protected function toastSuccess(int $userId, string $message, ?string $title = null): bool
    {
        return app(ToastService::class)->success($userId, $message, $title);
    }

    protected function toastError(int $userId, string $message, ?string $title = null): bool
    {
        return app(ToastService::class)->error($userId, $message, $title);
    }

    protected function toastWarning(int $userId, string $message, ?string $title = null): bool
    {
        return app(ToastService::class)->warning($userId, $message, $title);
    }

    protected function toastInfo(int $userId, string $message, ?string $title = null): bool
    {
        return app(ToastService::class)->info($userId, $message, $title);
    }
}