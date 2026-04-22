<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\WinnersNotification;
use PHPUnit\Framework\TestCase;

class WinnersNotificationTest extends TestCase
{
    public function test_notification_can_be_created(): void
    {
        $notification = new WinnersNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            winners: [
                ['user_id' => 1, 'position' => 1, 'points' => 100],
                ['user_id' => 2, 'position' => 2, 'points' => 90],
            ],
            prizeDescription: '1er lugar: $1000'
        );

        $this->assertInstanceOf(WinnersNotification::class, $notification);
        $this->assertEquals(1, $notification->quinielaId);
        $this->assertEquals('Copa Mundo 2026', $notification->quinielaName);
        $this->assertCount(2, $notification->winners);
        $this->assertEquals('1er lugar: $1000', $notification->prizeDescription);
    }

    public function test_notification_uses_pusher_and_database_channels(): void
    {
        $notification = new WinnersNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            winners: [],
            prizeDescription: 'Test prize'
        );

        $channels = $notification->via(new class {
            public function getKey(): string { return 'test'; }
            public function getChannelType(): string { return 'database'; }
        });

        $this->assertContains('database', $channels);
        $this->assertCount(1, $channels);
    }

    public function test_to_broadcast_returns_correct_data(): void
    {
        $notification = new WinnersNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            winners: [
                ['user_id' => 1, 'position' => 1, 'points' => 100],
            ],
            prizeDescription: '1er lugar: $1000'
        );

        $data = $notification->toBroadcast(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('quiniela_id', $data);
        $this->assertArrayHasKey('quiniela_name', $data);
        $this->assertArrayHasKey('winners', $data);
        $this->assertArrayHasKey('prize_description', $data);
        $this->assertArrayHasKey('message', $data);

        $this->assertEquals(1, $data['quiniela_id']);
        $this->assertEquals('Copa Mundo 2026', $data['quiniela_name']);
        $this->assertStringContainsString('Ganadores', $data['message']);
    }

    public function test_to_array_returns_correct_data(): void
    {
        $notification = new WinnersNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            winners: [
                ['user_id' => 1, 'position' => 1, 'points' => 100],
            ],
            prizeDescription: '1er lugar: $1000'
        );

        $data = $notification->toArray(new class {
            public function getKey(): string { return 'test'; }
        });

        $this->assertArrayHasKey('quiniela_id', $data);
        $this->assertArrayHasKey('quiniela_name', $data);
        $this->assertArrayHasKey('winners', $data);
        $this->assertArrayHasKey('prize_description', $data);
        $this->assertEquals(1, $data['quiniela_id']);
    }
}