<?php

namespace Tests\Feature\Notifications;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\DatabaseNotification;

class PollingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_poll_returns_notifications_since_timestamp(): void
    {
        // Create old notification (older than 10 minutes)
        $oldCreatedAt = Carbon::now()->subMinutes(15);
        
        $oldNotification = new DatabaseNotification([
            'id' => 'old-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Old',
                'description' => 'Old notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $oldCreatedAt,
        ]);
        
        $this->user->notifications()->save($oldNotification);

        // Create newer notification
        $newCreatedAt = Carbon::now()->subMinutes(5);
        
        $newNotification = new DatabaseNotification([
            'id' => 'new-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 2,
                'quinielaName' => 'Test New',
                'description' => 'New notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $newCreatedAt,
        ]);
        
        $this->user->notifications()->save($newNotification);

        // Poll for notifications since 10 minutes ago
        $tenMinutesAgo = Carbon::now()->subMinutes(10)->toISOString();
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll?since={$tenMinutesAgo}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', 'new-notification-id');
        $response->assertJsonPath('meta.has_new', true);
    }

    public function test_poll_returns_empty_when_no_new_notifications(): void
    {
        // Create old notification
        $oldCreatedAt = Carbon::now()->subMinutes(20);
        
        $oldNotification = new DatabaseNotification([
            'id' => 'old-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Old',
                'description' => 'Old notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $oldCreatedAt,
        ]);
        
        $this->user->notifications()->save($oldNotification);

        // Poll for notifications since 10 minutes ago
        $tenMinutesAgo = Carbon::now()->subMinutes(10)->toISOString();
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll?since={$tenMinutesAgo}");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
        $response->assertJsonPath('meta.has_new', false);
        $response->assertJsonPath('meta.count', 0);
    }

    public function test_poll_returns_has_new_flag_correctly(): void
    {
        // Create notification
        $createdAt = Carbon::now()->subMinutes(2);
        
        $notification = new DatabaseNotification([
            'id' => 'test-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test',
                'description' => 'Test notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $createdAt,
        ]);
        
        $this->user->notifications()->save($notification);

        $fiveMinutesAgo = Carbon::now()->subMinutes(5)->toISOString();
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll?since={$fiveMinutesAgo}");

        $response->assertStatus(200);
        $response->assertJsonPath('meta.has_new', true);
        $response->assertJsonPath('meta.count', 1);
    }

    public function test_poll_with_custom_since_parameter(): void
    {
        // Create a notification 30 minutes ago
        $thirtyMinutesAgo = Carbon::now()->subMinutes(30);
        $notification1 = new DatabaseNotification([
            'id' => 'notification-1',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test 30 min ago',
                'description' => 'Old notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $thirtyMinutesAgo,
        ]);
        $this->user->notifications()->save($notification1);

        // Create a notification 15 minutes ago
        $fifteenMinutesAgo = Carbon::now()->subMinutes(15);
        $notification2 = new DatabaseNotification([
            'id' => 'notification-2',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 2,
                'quinielaName' => 'Test 15 min ago',
                'description' => 'Mid notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $fifteenMinutesAgo,
        ]);
        $this->user->notifications()->save($notification2);

        // Create a notification 5 minutes ago
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $notification3 = new DatabaseNotification([
            'id' => 'notification-3',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 3,
                'quinielaName' => 'Test 5 min ago',
                'description' => 'Recent notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $fiveMinutesAgo,
        ]);
        $this->user->notifications()->save($notification3);

        // Poll for notifications since 20 minutes ago - should get 2 notifications
        $twentyMinutesAgo = Carbon::now()->subMinutes(20)->toISOString();
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll?since={$twentyMinutesAgo}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('meta.count', 2);
        $response->assertJsonPath('meta.has_new', true);
        
        // Verify we got the correct notifications (most recent first)
        $response->assertJsonPath('data.0.id', 'notification-3');
        $response->assertJsonPath('data.1.id', 'notification-2');
    }

    public function test_poll_returns_correct_meta_information(): void
    {
        // Create notification
        $createdAt = Carbon::now()->subMinutes(2);
        $notification = new DatabaseNotification([
            'id' => 'test-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test',
                'description' => 'Test notification',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $createdAt,
        ]);
        $this->user->notifications()->save($notification);

        $fiveMinutesAgo = Carbon::now()->subMinutes(5)->toISOString();
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll?since={$fiveMinutesAgo}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'last_check',
                'has_new',
                'count',
            ],
        ]);

        $response->assertJsonPath('meta.has_new', true);
        $response->assertJsonPath('meta.count', 1);
        $this->assertArrayHasKey('last_check', $response->json('meta'));
    }

    public function test_poll_default_since_parameter_works(): void
    {
        // Create notification older than default (5 minutes)
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $oldNotification = new DatabaseNotification([
            'id' => 'old-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Old',
                'description' => 'Default since test',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => $tenMinutesAgo,
        ]);
        $this->user->notifications()->save($oldNotification);

        // Call poll without since parameter - should use default (5 minutes ago)
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notifications/poll");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
        $response->assertJsonPath('meta.has_new', false);
    }
}