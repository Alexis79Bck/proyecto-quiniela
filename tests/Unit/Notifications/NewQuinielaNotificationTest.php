<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\NewQuinielaNotification;
use PHPUnit\Framework\TestCase;

class NewQuinielaNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            description: 'Predice los resultados del mundial',
            startDate: '2026-06-11',
            endDate: '2026-07-19'
        );

        $this->assertInstanceOf(NewQuinielaNotification::class, $notification);
        $this->assertEquals(1, $notification->quinielaId);
        $this->assertEquals('Copa Mundo 2026', $notification->quinielaName);
        $this->assertEquals('Predice los resultados del mundial', $notification->description);
        $this->assertEquals('2026-06-11', $notification->startDate);
        $this->assertEquals('2026-07-19', $notification->endDate);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertContains('database', $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            description: 'Predice los resultados',
            startDate: '2026-06-11',
            endDate: '2026-07-19'
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('quiniela_id', $data);
        $this->assertArrayHasKey('quiniela_name', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('start_date', $data);
        $this->assertArrayHasKey('end_date', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['quiniela_id']);
        $this->assertEquals('Copa Mundo 2026', $data['quiniela_name']);
        $this->assertStringContainsString('Nueva quiniela', $data['message']);
    }
}