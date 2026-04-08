<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\LeaderboardUpdateNotification;
use App\Infrastructure\Notifications\Channels\PusherChannel;
use PHPUnit\Framework\TestCase;

class LeaderboardUpdateNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new LeaderboardUpdateNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            topUsers: [
                ['user_id' => 1, 'name' => 'Juan', 'points' => 100],
                ['user_id' => 2, 'name' => 'Maria', 'points' => 95],
            ],
            totalParticipants: 50
        );

        $this->assertInstanceOf(LeaderboardUpdateNotification::class, $notification);
        $this->assertEquals(1, $notification->quinielaId);
        $this->assertEquals('Copa Mundo 2026', $notification->quinielaName);
        $this->assertCount(2, $notification->topUsers);
        $this->assertEquals(50, $notification->totalParticipants);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new LeaderboardUpdateNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            topUsers: [],
            totalParticipants: 10
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertContains(PusherChannel::class, $channels);
        $this->assertContains('database', $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new LeaderboardUpdateNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            topUsers: [
                ['user_id' => 1, 'name' => 'Juan', 'points' => 100],
            ],
            totalParticipants: 50
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('quiniela_id', $data);
        $this->assertArrayHasKey('quiniela_name', $data);
        $this->assertArrayHasKey('top_users', $data);
        $this->assertArrayHasKey('total_participants', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['quiniela_id']);
        $this->assertEquals(50, $data['total_participants']);
        $this->assertStringContainsString('Clasificación', $data['message']);
    }
}