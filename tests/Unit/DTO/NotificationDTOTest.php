<?php

namespace Tests\Unit\DTO;

use App\DTO\NotificationDTO;
use App\Infrastructure\Notifications\NewQuinielaNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class NotificationDTOTest extends TestCase
{
    use RefreshDatabase;

    public function test_dto_creates_from_model_correctly(): void
    {
        // Create a fake notification model
        $notificationData = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel = new DatabaseNotification($notificationData);

        $dto = NotificationDTO::fromModel($notificationModel);

        $this->assertInstanceOf(NotificationDTO::class, $dto);
        $this->assertEquals('test-notification-id', $dto->id);
        $this->assertEquals(NewQuinielaNotification::class, $dto->type);
        $this->assertNull($dto->readAt);
        $this->assertInstanceOf(Carbon::class, $dto->createdAt);
    }

    public function test_dto_extracts_title_from_data(): void
    {
        // Test with title in data
        $notificationDataWithTitle = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'title' => 'Custom Title',
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel = new DatabaseNotification($notificationDataWithTitle);
        $dto = NotificationDTO::fromModel($notificationModel);

        $this->assertEquals('Custom Title', $dto->title);

        // Test with default title based on type
        $notificationDataWithoutTitle = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel2 = new DatabaseNotification($notificationDataWithoutTitle);
        $dto2 = NotificationDTO::fromModel($notificationModel2);

        $this->assertEquals('Nueva Quiniela Disponible', $dto2->title);
    }

    public function test_dto_extracts_message_from_data(): void
    {
        // Test with message in data
        $notificationDataWithMessage = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'message' => 'Custom Message',
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel = new DatabaseNotification($notificationDataWithMessage);
        $dto = NotificationDTO::fromModel($notificationModel);

        $this->assertEquals('Custom Message', $dto->message);

        // Test with default message based on type
        $notificationDataWithoutMessage = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel2 = new DatabaseNotification($notificationDataWithoutMessage);
        $dto2 = NotificationDTO::fromModel($notificationModel2);

        $this->assertEquals('Hay una nueva quiniela disponible para unirse.', $dto2->message);
    }

    public function test_dto_to_array_returns_correct_structure(): void
    {
        $createdAt = Carbon::now();
        $readAt = Carbon::now()->addHour();

        $notificationData = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => $readAt,
            'created_at' => $createdAt,
        ];

        $notificationModel = new DatabaseNotification($notificationData);
        $dto = NotificationDTO::fromModel($notificationModel);
        $array = $dto->toArray();

        $expectedKeys = ['id', 'type', 'title', 'message', 'data', 'read_at', 'created_at'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $array);
        }

        $this->assertEquals('test-notification-id', $array['id']);
        $this->assertEquals(NewQuinielaNotification::class, $array['type']);
        
        // Compare timestamps without microseconds due to precision differences
        $this->assertEquals($readAt->timestamp, Carbon::parse($array['read_at'])->timestamp);
        $this->assertEquals($createdAt->timestamp, Carbon::parse($array['created_at'])->timestamp);
    }

    public function test_dto_handles_null_read_at(): void
    {
        $notificationData = [
            'id' => 'test-notification-id',
            'type' => NewQuinielaNotification::class,
            'data' => [
                'quinielaId' => 1,
                'quinielaName' => 'Test Quiniela',
                'description' => 'Test Description',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
            ],
            'read_at' => null,
            'created_at' => now(),
        ];

        $notificationModel = new DatabaseNotification($notificationData);
        $dto = NotificationDTO::fromModel($notificationModel);

        $this->assertNull($dto->readAt);
        
        $array = $dto->toArray();
        $this->assertNull($array['read_at']);
    }

    public function test_dto_with_different_notification_types(): void
    {
        $notificationTypes = [
            'App\\Infrastructure\\Notifications\\NewQuinielaNotification' => 'Nueva Quiniela Disponible',
            'App\\Infrastructure\\Notifications\\WinnersNotification' => 'Ganadores Anunciados',
            'App\\Infrastructure\\Notifications\\PredictionReminderNotification' => 'Recordatorio de Predicción',
            'App\\Infrastructure\\Notifications\\MatchStartedNotification' => 'Partido Comenzado',
            'App\\Infrastructure\\Notifications\\MatchResultNotification' => 'Resultado del Partido',
            'App\\Infrastructure\\Notifications\\LeaderboardUpdateNotification' => 'Actualización de Tabla',
        ];

        foreach ($notificationTypes as $type => $expectedTitle) {
            $notificationData = [
                'id' => 'test-notification-id',
                'type' => $type,
                'data' => [],
                'read_at' => null,
                'created_at' => now(),
            ];

            $notificationModel = new DatabaseNotification($notificationData);
            $dto = NotificationDTO::fromModel($notificationModel);

            $this->assertEquals($expectedTitle, $dto->title, "Failed for type: {$type}");
        }
    }
}