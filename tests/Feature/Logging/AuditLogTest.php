<?php

namespace Tests\Feature\Logging;

use App\Events\LogAuditEvent;
use App\Infrastructure\Logging\AuditLogger\AuditLogger;
use App\Listeners\AuditLogListener;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_logger_persists_audit_log_with_usuario_id_and_spanish_fields(): void
    {
        $user = User::factory()->create();

        $logger = $this->app->make(AuditLogger::class);

        $logger->log(
            'login_success',
            'user',
            $user->id,
            ['status' => 'pending'],
            ['status' => 'completed'],
            ['source' => 'test'],
            'audit',
            $user->id,
            '127.0.0.1',
            'PHPUnit-Agent/1.0'
        );

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $user->id,
            'accion' => 'login_success',
            'tipo_entidad' => 'user',
            'entity_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit-Agent/1.0',
        ]);
    }

    public function test_audit_logger_helper_methods_create_expected_audit_logs(): void
    {
        $user = User::factory()->create();
        $logger = $this->app->make(AuditLogger::class);

        $logger->logLogin($user->id, ['source' => 'test']);
        $logger->logLogout($user->id, ['source' => 'test']);
        $logger->logRegister($user->id, ['source' => 'test']);
        $logger->logFailedLogin('test@example.com', ['reason' => 'wrong_password']);
        $logger->logApiRequest('GET', '/api/test', ['source' => 'test']);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $user->id,
            'accion' => 'login',
            'tipo_entidad' => 'user',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $user->id,
            'accion' => 'logout',
            'tipo_entidad' => 'user',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $user->id,
            'accion' => 'register',
            'tipo_entidad' => 'user',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => null,
            'accion' => 'failed_login',
            'tipo_entidad' => null,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => null,
            'accion' => 'api_request',
            'tipo_entidad' => null,
        ]);
    }

    public function test_event_listener_handles_log_audit_event_and_persists_audit_log(): void
    {
        $user = User::factory()->create();

        $event = new LogAuditEvent(
            $user->id,
            'custom_audit',
            'user',
            $user->id,
            ['field' => 'old'],
            ['field' => 'new'],
            '127.0.0.1',
            'PHPUnit-Agent/1.0',
            ['source' => 'event'],
            'audit'
        );

        $listener = $this->app->make(AuditLogListener::class);
        $listener->handle($event);

        $this->assertDatabaseHas('audit_logs', [
            'usuario_id' => $user->id,
            'accion' => 'custom_audit',
            'tipo_entidad' => 'user',
            'entity_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit-Agent/1.0',
        ]);
    }

    public function test_audit_log_model_scopes_and_formatted_helpers(): void
    {
        $user = User::factory()->create();

        $auditLog = AuditLog::create([
            'usuario_id' => $user->id,
            'accion' => 'prediction_update',
            'tipo_entidad' => 'prediction',
            'entity_id' => 123,
            'old_values' => ['score' => 1],
            'new_values' => ['score' => 2],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit-Agent/1.0',
            'metadata' => ['source' => 'test'],
        ]);

        $this->assertSame(['score' => 1], $auditLog->old_values);
        $this->assertSame(['score' => 2], $auditLog->new_values);
        $this->assertSame(['source' => 'test'], $auditLog->metadata);
        $this->assertStringContainsString('"score": 1', $auditLog->formatted_old_values);
        $this->assertStringContainsString('"score": 2', $auditLog->formatted_new_values);
        $this->assertStringContainsString('"source": "test"', $auditLog->formatted_metadata);
        $this->assertTrue($auditLog->hasAuditChanges());
        $this->assertSame(['score' => ['old' => 1, 'new' => 2]], $auditLog->getChangesSummary());
        $this->assertTrue(AuditLog::byAction('prediction_update')->exists());
        $this->assertTrue(AuditLog::byEntityType('prediction')->exists());
        $this->assertTrue(AuditLog::byEntity('prediction', 123)->exists());
        $this->assertTrue(AuditLog::byUser($user->id)->exists());
        $this->assertTrue(AuditLog::byIpAddress('127.0.0.1')->exists());
        $this->assertTrue(AuditLog::byActionType('prediction')->exists());
    }
}
