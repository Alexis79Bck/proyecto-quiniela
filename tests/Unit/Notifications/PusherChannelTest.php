<?php

namespace Tests\Unit\Notifications;

use App\Infrastructure\Notifications\Channels\PusherChannel;
use App\Infrastructure\Notifications\NewQuinielaNotification;
use PHPUnit\Framework\TestCase;
use Mockery;

class PusherChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_channel_instantiates_with_pusher(): void
    {
        $pusher = Mockery::mock('Pusher\Pusher');
        
        $channel = new PusherChannel($pusher);
        
        $this->assertInstanceOf(PusherChannel::class, $channel);
    }

    public function test_send_calls_pusher_trigger(): void
    {
        $pusher = Mockery::mock('Pusher\Pusher');
        $pusher->shouldReceive('trigger')->once()->andReturn(true);
        
        $channel = new PusherChannel($pusher);
        
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test description',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        );
        
        $notifiable = new class {
            public function getKey(): string { return 'test-key'; }
        };
        
        $channel->send($notifiable, $notification);
        
        $this->assertTrue(true);
    }

    public function test_get_channels_returns_default_when_broadcast_on_not_defined(): void
    {
        $pusher = Mockery::mock('Pusher\Pusher');
        
        $channel = new PusherChannel($pusher);
        
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        );
        
        $notifiable = new class {
            public function getKey(): string { return 'test-key'; }
        };
        
        $reflection = new \ReflectionClass($channel);
        $method = $reflection->getMethod('getChannels');
        $method->setAccessible(true);
        
        $channels = $method->invoke($channel, $notifiable, $notification);
        
        $this->assertIsArray($channels);
    }
}