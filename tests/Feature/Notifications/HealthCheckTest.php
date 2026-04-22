<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_health_returns_ok_status(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/health');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'ok');
    }

    public function test_health_returns_correct_counts(): void
    {
        // Initially no notifications
        $response1 = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/health');

        $response1->assertStatus(200);
        $response1->assertJsonPath('unread_count', 0);
        $response1->assertJsonPath('total_count', 0);

        // Create 3 notifications
        for ($i = 1; $i <= 3; $i++) {
            $createdAt = Carbon::now()->subMinutes(3 - $i); // Different timestamps
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

        // Health check should show 3 total, 3 unread
        $response2 = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/health');

        $response2->assertStatus(200);
        $response2->assertJsonPath('unread_count', 3);
        $response2->assertJsonPath('total_count', 3);

        // Mark one as read by updating the database directly
        $notificationToUpdate = $this->user->notifications()->first();
        $notificationToUpdate->update(['read_at' => now()]);

        // Health check should show 3 total, 2 unread
        $response3 = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/health');

        $response3->assertStatus(200);
        $response3->assertJsonPath('unread_count', 2);
        $response3->assertJsonPath('total_count', 3);
    }

    public function test_health_returns_timestamp(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'unread_count',
            'total_count',
            'timestamp',
        ]);

        // Validate timestamp is a valid ISO string
        $timestamp = $response->json('timestamp');
        $this->assertIsString($timestamp);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $timestamp);
    }

    public function test_health_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/notifications/health');

        $response->assertStatus(401);
    }
}