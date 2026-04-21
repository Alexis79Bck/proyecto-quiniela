<?php

namespace Tests\Feature\Notifications;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Infrastructure\Notifications\NewQuinielaNotification;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = Usuario::factory()->create();
    }

    public function test_user_can_get_notifications(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
    }

    public function test_user_can_get_unread_notifications(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/unread');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta',
        ]);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $notification = $this->user->unreadNotifications()->first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Notificación marcada como leída',
            ]);

        $this->user->refresh();
        $this->assertNotNull($this->user->notifications()->first()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test 1',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 2,
            quinielaName: 'Test 2',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Todas las notificaciones marcadas como leídas',
            ]);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Notificación eliminada',
            ]);

        $this->assertEquals(0, $this->user->notifications()->count());
    }

    public function test_user_can_get_notification_count(): void
    {
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Test',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));
        $this->user->notify(new NewQuinielaNotification(
            quinielaId: 2,
            quinielaName: 'Test 2',
            description: 'Test',
            startDate: '2026-01-01',
            endDate: '2026-12-31'
        ));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/count');

        $response->assertStatus(200);
        $response->assertJson([
            'unread_count' => 2,
            'total_count' => 2,
        ]);
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->getJson('/api/notifications');

        $response->assertStatus(401);
    }

    public function test_user_can_mark_nonexistent_notification_as_read(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/notifications/non-existent-id/read');

        $response->assertStatus(404);
    }
}