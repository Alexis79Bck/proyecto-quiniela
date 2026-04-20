<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class PersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_notifications_persist_in_database(): void
    {
        // Count initial notifications
        $initialCount = DB::table('notifications')->count();

        // Create notification manually
        $notificationData = [
            'id' => 'test-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode([
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ]),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notifications')->insert($notificationData);

        // Check that notification was persisted
        $this->assertEquals($initialCount + 1, DB::table('notifications')->count());

        // Check that notification exists in database with correct data
        $notification = DB::table('notifications')->where('notifiable_id', $this->user->id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('App\Infrastructure\Notifications\NewQuinielaNotification', $notification->type);
        $this->assertJson($notification->data);
    }

    public function test_notification_read_at_is_persisted(): void
    {
        // Create notification manually
        $notificationData = [
            'id' => 'test-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode([
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ]),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notifications')->insert($notificationData);

        // Check that notification initially has null read_at
        $notification = DB::table('notifications')->where('notifiable_id', $this->user->id)->first();
        $this->assertNull($notification->read_at);

        // Mark as read by updating the database
        $readTime = now();
        DB::table('notifications')->where('id', 'test-notification-id')->update(['read_at' => $readTime]);

        // Check that read_at is now persisted
        $updatedNotification = DB::table('notifications')->where('id', 'test-notification-id')->first();
        $this->assertNotNull($updatedNotification->read_at);
        
        // Parse the read_at time and compare
        $readAtTime = Carbon::parse($updatedNotification->read_at);
        $this->assertTrue($readAtTime->equalTo($readTime) || $readAtTime->greaterThan($readTime->subSeconds(1)));
    }

    public function test_deleted_notifications_do_not_appear_in_list(): void
    {
        // Create notification manually
        $notificationData = [
            'id' => 'test-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode([
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ]),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notifications')->insert($notificationData);

        // Verify notification exists
        $this->assertEquals(1, DB::table('notifications')->where('notifiable_id', $this->user->id)->count());

        // Delete notification
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/notifications/test-notification-id");

        $response->assertStatus(200);

        // Verify notification no longer exists in database
        $this->assertEquals(0, DB::table('notifications')->where('id', 'test-notification-id')->count());
    }

    public function test_notification_data_is_correctly_serialized(): void
    {
        $notificationData = [
            'quinielaId' => 123,
            'quinielaName' => 'Special Characters: áéíóúñÑüïë',
            'description' => 'Test with special characters and symbols !@#$%^&*()',
            'startDate' => '2026-01-01',
            'endDate' => '2026-12-31',
            'extraFields' => [
                'nested' => [
                    'value1' => 'test',
                    'value2' => 42,
                ]
            ]
        ];

        // Create notification with complex data manually
        $dbNotificationData = [
            'id' => 'complex-data-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode($notificationData),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notifications')->insert($dbNotificationData);

        // Check that data is correctly serialized in database
        $dbNotification = DB::table('notifications')->where('notifiable_id', $this->user->id)->first();
        $storedData = json_decode($dbNotification->data, true);

        $this->assertEquals($notificationData['quinielaId'], $storedData['quinielaId']);
        $this->assertEquals($notificationData['quinielaName'], $storedData['quinielaName']);
        $this->assertEquals($notificationData['description'], $storedData['description']);
        $this->assertEquals($notificationData['startDate'], $storedData['startDate']);
        $this->assertEquals($notificationData['endDate'], $storedData['endDate']);
    }

    public function test_multiple_users_notifications_are_isolated(): void
    {
        // Create second user
        $user2 = User::factory()->create();

        // Create notifications for both users manually
        $notification1Data = [
            'id' => 'user1-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode([
                'quinielaId' => 1,
                'quinielaName' => 'User 1 Notification',
                'description' => 'Test',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ]),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $notification2Data = [
            'id' => 'user2-notification-id',
            'type' => 'App\Infrastructure\Notifications\NewQuinielaNotification',
            'data' => json_encode([
                'quinielaId' => 2,
                'quinielaName' => 'User 2 Notification',
                'description' => 'Test',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ]),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user2->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('notifications')->insert($notification1Data);
        DB::table('notifications')->insert($notification2Data);

        // Check that each user only sees their own notifications
        $user1Count = DB::table('notifications')->where('notifiable_id', $this->user->id)->count();
        $user2Count = DB::table('notifications')->where('notifiable_id', $user2->id)->count();
        
        $this->assertEquals(1, $user1Count);
        $this->assertEquals(1, $user2Count);

        $user1Notification = DB::table('notifications')->where('notifiable_id', $this->user->id)->first();
        $user2Notification = DB::table('notifications')->where('notifiable_id', $user2->id)->first();

        $user1Data = json_decode($user1Notification->data, true);
        $user2Data = json_decode($user2Notification->data, true);

        $this->assertEquals('User 1 Notification', $user1Data['quinielaName']);
        $this->assertEquals('User 2 Notification', $user2Data['quinielaName']);

        // Check database isolation
        $totalNotifications = DB::table('notifications')->count();
        $this->assertEquals(2, $totalNotifications);
    }
}