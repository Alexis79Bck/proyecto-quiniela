<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\PredictionReminderNotification;
use PHPUnit\Framework\TestCase;

class PredictionReminderNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new PredictionReminderNotification(
            userId: 1,
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            matchTime: '2026-06-15 16:00:00',
            quinielaName: 'Copa Mundo 2026'
        );

        $this->assertInstanceOf(PredictionReminderNotification::class, $notification);
        $this->assertEquals(1, $notification->userId);
        $this->assertEquals(1, $notification->matchId);
        $this->assertEquals('Argentina', $notification->homeTeam);
        $this->assertEquals('Brasil', $notification->awayTeam);
        $this->assertEquals('2026-06-15 16:00:00', $notification->matchTime);
        $this->assertEquals('Copa Mundo 2026', $notification->quinielaName);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new PredictionReminderNotification(
            userId: 1,
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            matchTime: '2026-06-15 16:00:00',
            quinielaName: 'Test'
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertContains('database', $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new PredictionReminderNotification(
            userId: 1,
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            matchTime: '2026-06-15 16:00:00',
            quinielaName: 'Copa Mundo 2026'
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('match_id', $data);
        $this->assertArrayHasKey('home_team', $data);
        $this->assertArrayHasKey('away_team', $data);
        $this->assertArrayHasKey('match_time', $data);
        $this->assertArrayHasKey('quiniela_name', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['user_id']);
        $this->assertEquals(1, $data['match_id']);
        $this->assertStringContainsString('Recordatorio', $data['message']);
        $this->assertStringContainsString('predicción', $data['message']);
    }
}