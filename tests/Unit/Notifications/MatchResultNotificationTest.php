<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\MatchResultNotification;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use PHPUnit\Framework\TestCase;

class MatchResultNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new MatchResultNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            homeScore: 3,
            awayScore: 2,
            stage: 'Final'
        );

        $this->assertInstanceOf(MatchResultNotification::class, $notification);
        $this->assertEquals(1, $notification->matchId);
        $this->assertEquals('Argentina', $notification->homeTeam);
        $this->assertEquals('Brasil', $notification->awayTeam);
        $this->assertEquals(3, $notification->homeScore);
        $this->assertEquals(2, $notification->awayScore);
        $this->assertEquals('Final', $notification->stage);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new MatchResultNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            homeScore: 3,
            awayScore: 2,
            stage: 'Final'
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertContains(PusherChannel::class, $channels);
        $this->assertContains('database', $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new MatchResultNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            homeScore: 3,
            awayScore: 2,
            stage: 'Final'
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('match_id', $data);
        $this->assertArrayHasKey('home_team', $data);
        $this->assertArrayHasKey('away_team', $data);
        $this->assertArrayHasKey('home_score', $data);
        $this->assertArrayHasKey('away_score', $data);
        $this->assertArrayHasKey('stage', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['match_id']);
        $this->assertEquals(3, $data['home_score']);
        $this->assertEquals(2, $data['away_score']);
        $this->assertStringContainsString('3', $data['message']);
        $this->assertStringContainsString('2', $data['message']);
    }
}