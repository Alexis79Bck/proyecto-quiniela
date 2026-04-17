<?php

namespace Tests\Feature\Notifications;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\DatabaseNotification;

class LatestTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_latest_returns_specified_number_of_notifications(): void
    {
        // Create 5 notifications
        for ($i = 1; $i <= 5; $i++) {
            $createdAt = Carbon::now()->addMinutes($i);
            $notification = new DatabaseNotification([
                'id' => "notification-{$i}",
                'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
                'data' => [
                    'quinielaId' => $i,
                    'quinielaName' => "Test Quiniela {$i}",
                    'description' => "Description {$i}",
                    'startDate' => '2026-01-01',
                    'endDate' => '2026-12-31',
                ],
                'read_at' => null,
                'created_at' => $createdAt,
            ]);
            
            $this->user->notifications()->save($notification);
        }

        // Request latest 3 notifications
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/latest?limit=3');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonPath('meta.count', 3);
        
        // Check that notifications are in descending order (most recent first)
        $notifications = $response->json('data');
        $this->assertGreaterThan($notifications[1]['created_at'], $notifications[0]['created_at']);
        $this->assertGreaterThan($notifications[2]['created_at'], $notifications[1]['created_at']);
    }

    public function test_latest_returns_default_10_notifications(): void
    {
        // Create 12 notifications
        for ($i = 1; $i <= 12; $i++) {
            $createdAt = Carbon::now()->addMinutes($i);
            $notification = new DatabaseNotification([
                'id' => "notification-{$i}",
                'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
                'data' => [
                    'quinielaId' => $i,
                    'quinielaName' => "Test Quiniela {$i}",
                    'description' => "Description {$i}",
                    'startDate' => '2026-01-01',
                    'endDate' => '2026-12-31',
                ],
                'read_at' => null,
                'created_at' => $createdAt,
            ]);
            
            $this->user->notifications()->save($notification);
        }

        // Request latest notifications without specifying limit (should default to 10)
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/latest');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data'); // Should return 10 (default limit)
        $response->assertJsonPath('meta.count', 10);
    }

    public function test_latest_returns_notifications_in_descending_order(): void
    {
        // Create notifications one after another to establish chronological order
        $notificationsData = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $createdAt = Carbon::now()->addMinutes($i);
            $notification = new DatabaseNotification([
                'id' => "notification-{$i}",
                'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
                'data' => [
                    'quinielaId' => $i,
                    'quinielaName' => "Quiniela {$i}",
                    'description' => "Description {$i}",
                    'startDate' => '2026-01-01',
                    'endDate' => '2026-12-31',
                ],
                'read_at' => null,
                'created_at' => $createdAt,
            ]);
            
            $this->user->notifications()->save($notification);
            $notificationsData[] = "Quiniela {$i}";
        }

        // Request latest notifications
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/latest');

        $response->assertStatus(200);
        
        // Check that notifications are returned in descending order (most recent first)
        $data = $response->json('data');
        $this->assertCount(5, $data);
        
        // Most recent notification should be first
        $this->assertEquals("notification-5", $data[0]['id']);
        $this->assertEquals("notification-4", $data[1]['id']);
        $this->assertEquals("notification-3", $data[2]['id']);
        $this->assertEquals("notification-2", $data[3]['id']);
        $this->assertEquals("notification-1", $data[4]['id']);
    }

    public function test_latest_with_limit_zero_returns_empty(): void
    {
        // Create notifications
        for ($i = 1; $i <= 3; $i++) {
            $createdAt = Carbon::now()->addMinutes($i);
            $notification = new DatabaseNotification([
                'id' => "notification-{$i}",
                'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
                'data' => [
                    'quinielaId' => $i,
                    'quinielaName' => "Test Quiniela {$i}",
                    'description' => "Description {$i}",
                    'startDate' => '2026-01-01',
                    'endDate' => '2026-12-31',
                ],
                'read_at' => null,
                'created_at' => $createdAt,
            ]);
            
            $this->user->notifications()->save($notification);
        }

        // Request latest with limit 0
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/latest?limit=0');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
        $response->assertJsonPath('meta.count', 0);
    }

    public function test_latest_with_large_limit_returns_all_available(): void
    {
        // Create 3 notifications
        for ($i = 1; $i <= 3; $i++) {
            $createdAt = Carbon::now()->addMinutes($i);
            $notification = new DatabaseNotification([
                'id' => "notification-{$i}",
                'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
                'data' => [
                    'quinielaId' => $i,
                    'quinielaName' => "Test Quiniela {$i}",
                    'description' => "Description {$i}",
                    'startDate' => '2026-01-01',
                    'endDate' => '2026-12-31',
                ],
                'read_at' => null,
                'created_at' => $createdAt,
            ]);
            
            $this->user->notifications()->save($notification);
        }

        // Request latest with large limit
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/latest?limit=100');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data'); // Should only return 3 since that's all we have
        $response->assertJsonPath('meta.count', 3);
    }
}