<?php

// Este test se eliminó porque el proyecto no implementa Pusher, sino persistencia + polling.
// Las notificaciones ahora usan solo el canal 'database'.

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