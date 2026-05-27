<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'nombre_completo' => 'Test User',
            'nombre_usuario' => 'testuser',
            'correo_electronico' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Usuario creado con éxito',
        ]);
        $this->assertNotNull($response->json('token'));
        $this->assertNull($response->json('data')); // Since the user is not an admin

        $this->assertDatabaseHas('usuarios', [
            'correo_electronico' => 'test@example.com',
            'nombre_completo' => 'Test User',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Sesión iniciada correctamente',
        ]);
        $this->assertNotNull($response->json('token'));
        $this->assertNull($response->json('data')); // Since the user is not an admin

        // Optionally, you can also assert that the token belongs to the user
        // but we trust that the token is generated correctly.
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Las credenciales proporcionadas son incorrectas.',
            'data' => null,
        ]);
    }

    public function test_user_can_logout(): void
    {
        $user = Usuario::factory()->create();
        $token = $user->createToken('test-token');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_own_info(): void
    {
        $user = Usuario::factory()->create([
            'nombre_completo' => 'Test User',
            'correo_electronico' => 'test@example.com',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/me');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'nombre_completo' => 'Test User',
            'correo_electronico' => 'test@example.com',
        ]);
    }

    public function test_registration_fails_with_existing_email(): void
    {
        $user = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'nombre_completo' => 'Another User',
            'nombre_usuario' => 'anotheruser',
            'correo_electronico' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Este correo electrónico ya está registrado.',
            'data' => null,
        ]);
    }
}
