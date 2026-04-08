<?php

namespace App\Enums;

enum ToastType: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';

    public function getIcon(): string
    {
        return match ($this) {
            self::SUCCESS => 'check_circle',
            self::ERROR => 'error',
            self::WARNING => 'warning',
            self::INFO => 'info',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SUCCESS => '#10b981',
            self::ERROR => '#ef4444',
            self::WARNING => '#f59e0b',
            self::INFO => '#3b82f6',
        };
    }

    public function getDefaultTitle(): string
    {
        return match ($this) {
            self::SUCCESS => 'Éxito',
            self::ERROR => 'Error',
            self::WARNING => 'Advertencia',
            self::INFO => 'Información',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}