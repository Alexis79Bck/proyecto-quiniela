<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\MatchStartedNotification;
use PHPUnit\Framework\TestCase;

class MatchStartedNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new MatchStartedNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            startTime: '2026-06-15 16:00:00',
            stage: 'Final'
        );

        $this->assertInstanceOf(MatchStartedNotification::class, $notification);
        $this->assertEquals(1, $notification->matchId);
        $this->assertEquals('Argentina', $notification->homeTeam);
        $this->assertEquals('Brasil', $notification->awayTeam);
        $this->assertEquals('2026-06-15 16:00:00', $notification->startTime);
        $this->assertEquals('Final', $notification->stage);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new MatchStartedNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            startTime: '2026-06-15 16:00:00',
            stage: 'Final'
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertContains('database', $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new MatchStartedNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            startTime: '2026-06-15 16:00:00',
            stage: 'Final'
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('match_id', $data);
        $this->assertArrayHasKey('home_team', $data);
        $this->assertArrayHasKey('away_team', $data);
        $this->assertArrayHasKey('start_time', $data);
        $this->assertArrayHasKey('stage', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['match_id']);
        $this->assertEquals('Argentina', $data['home_team']);
        $this->assertEquals('Brasil', $data['away_team']);
        $this->assertStringContainsString('Argentina', $data['message']);
        $this->assertStringContainsString('Brasil', $data['message']);
    }
}