<?php

namespace Tests\Feature\Toast;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToastApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_broadcast_toast(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/toasts/broadcast', [
                'type' => 'success',
                'message' => 'Operation completed successfully',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'type',
                    'title',
                    'message',
                    'icon',
                    'color',
                    'duration',
                    'dismissible',
                    'timestamp',
                ],
            ]);
    }

    public function test_can_get_toast_types(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/toasts/types');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'types' => [
                    '*' => [
                        'type',
                        'icon',
                        'color',
                        'default_title',
                    ],
                ],
            ]);

        $types = $response->json('types');
        $this->assertCount(4, $types);
    }

    public function test_can_create_toast_without_broadcast(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/toasts', [
                'type' => 'warning',
                'message' => 'This is a warning',
                'title' => 'Warning Title',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Toast creado correctamente',
            ]);

        $this->assertEquals('warning', $response->json('data.type'));
        $this->assertEquals('Warning Title', $response->json('data.title'));
    }

    public function test_broadcast_requires_type_field(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/toasts/broadcast', [
                'message' => 'Test message',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_broadcast_requires_message_field(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/toasts/broadcast', [
                'type' => 'success',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_broadcast_validates_type_enum(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/toasts/broadcast', [
                'type' => 'invalid_type',
                'message' => 'Test message',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
}