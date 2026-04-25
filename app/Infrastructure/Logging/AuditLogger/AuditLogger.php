<?php

namespace App\Infrastructure\Logging\AuditLogger;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function __construct(
        protected AuditLogRepositoryInterface $auditLogRepository
    ) {}

    /**
     * Log an authentication event.
     */
    public function logAuth(string $action, ?int $userId = null, array $metadata = []): void
    {
        $this->log($action, 'user', $userId, null, null, $metadata);
    }

    /**
     * Log a quiniela event.
     */
    public function logQuiniela(string $action, int $quinielaId, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): void
    {
        $this->log($action, 'quiniela', $quinielaId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log a prediction event.
     */
    public function logPrediction(string $action, int $predictionId, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): void
    {
        $this->log($action, 'prediction', $predictionId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log a scoring event.
     */
    public function logScoring(string $action, ?int $entityId = null, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): void
    {
        $this->log($action, 'score', $entityId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log an admin event.
     */
    public function logAdmin(string $action, ?string $entityType = null, ?int $entityId = null, ?array $oldValues = null, ?array $newValues = null, array $metadata = []): void
    {
        $this->log($action, $entityType, $entityId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log a security event.
     */
    public function logSecurity(string $action, array $metadata = []): void
    {
        $this->log($action, null, null, null, null, $metadata, 'security');
    }

    /**
     * Log an API event.
     */
    public function logApi(string $action, array $metadata = []): void
    {
        $this->log($action, null, null, null, null, $metadata, 'api');
    }

    /**
     * Main logging method.
     *
     * @param  string  $action  The action being logged
     * @param  string|null  $entityType  The type of entity (e.g., 'user', 'quiniela')
     * @param  int|null  $entityId  The ID of the entity
     * @param  array|null  $oldValues  The old values before the change
     * @param  array|null  $newValues  The new values after the change
     * @param  array  $metadata  Additional metadata
     * @param  string  $logChannel  The log channel to use
     * @param  int|null  $userId  Optional user ID (uses auth context if not provided)
     * @param  string|null  $ipAddress  Optional IP address (uses request context if not provided)
     * @param  string|null  $userAgent  Optional user agent (uses request context if not provided)
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = [],
        string $logChannel = 'audit',
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        // Use provided values or fall back to request context
        $userId = $userId ?? auth()->id();
        $ipAddress = $ipAddress ?? Request::ip();
        $userAgent = $userAgent ?? Request::userAgent();

        // Prepare log data
        $logData = [
            'usuario_id' => $userId,
            'accion' => $action,
            'tipo_entidad' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'metadata' => $metadata,
        ];

        // Log to file
        $this->logToFile($logChannel, $logData);

        // Log to database
        $this->logToDatabase($logData);
    }

    /**
     * Log to file using Laravel's Log facade.
     */
    protected function logToFile(string $channel, array $data): void
    {
        $message = $this->formatLogMessage($data);

        Log::channel($channel)->info($message, $data);
    }

    /**
     * Log to database using repository.
     */
    protected function logToDatabase(array $data): void
    {
        try {
            $this->auditLogRepository->createLog($data);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::channel('security')->error('Failed to create audit log in database', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Format log message for file logging.
     */
    protected function formatLogMessage(array $data): string
    {
        $parts = [];

        if ($data['usuario_id']) {
            $parts[] = "User #{$data['usuario_id']}";
        }

        $parts[] = $data['accion'];

        if ($data['tipo_entidad']) {
            $parts[] = "{$data['tipo_entidad']}";
            if ($data['entity_id']) {
                $parts[] = "#{$data['entity_id']}";
            }
        }

        if ($data['ip_address']) {
            $parts[] = "IP: {$data['ip_address']}";
        }

        return implode(' | ', $parts);
    }

    /**
     * Log login event.
     */
    public function logLogin(int $userId, array $metadata = []): void
    {
        $this->logAuth('login', $userId, array_merge($metadata, [
            'login_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log logout event.
     */
    public function logLogout(int $userId, array $metadata = []): void
    {
        $this->logAuth('logout', $userId, array_merge($metadata, [
            'logout_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log registration event.
     */
    public function logRegister(int $userId, array $metadata = []): void
    {
        $this->logAuth('register', $userId, array_merge($metadata, [
            'registered_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log failed login attempt.
     */
    public function logFailedLogin(string $email, array $metadata = []): void
    {
        $this->logSecurity('failed_login', array_merge($metadata, [
            'email' => $email,
            'attempted_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log unauthorized access attempt.
     */
    public function logUnauthorizedAccess(string $route, array $metadata = []): void
    {
        $this->logSecurity('unauthorized_access', array_merge($metadata, [
            'route' => $route,
            'attempted_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log quiniela creation.
     */
    public function logQuinielaCreate(int $quinielaId, array $data, array $metadata = []): void
    {
        $this->logQuiniela('quiniela_create', $quinielaId, null, $data, $metadata);
    }

    /**
     * Log quiniela update.
     */
    public function logQuinielaUpdate(int $quinielaId, array $oldValues, array $newValues, array $metadata = []): void
    {
        $this->logQuiniela('quiniela_update', $quinielaId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log quiniela deletion.
     */
    public function logQuinielaDelete(int $quinielaId, array $data, array $metadata = []): void
    {
        $this->logQuiniela('quiniela_delete', $quinielaId, $data, null, $metadata);
    }

    /**
     * Log prediction creation.
     */
    public function logPredictionCreate(int $predictionId, array $data, array $metadata = []): void
    {
        $this->logPrediction('prediction_create', $predictionId, null, $data, $metadata);
    }

    /**
     * Log prediction update.
     */
    public function logPredictionUpdate(int $predictionId, array $oldValues, array $newValues, array $metadata = []): void
    {
        $this->logPrediction('prediction_update', $predictionId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log prediction deletion.
     */
    public function logPredictionDelete(int $predictionId, array $data, array $metadata = []): void
    {
        $this->logPrediction('prediction_delete', $predictionId, $data, null, $metadata);
    }

    /**
     * Log score calculation.
     */
    public function logScoreCalculate(int $scoreId, array $data, array $metadata = []): void
    {
        $this->logScoring('score_calculate', $scoreId, null, $data, $metadata);
    }

    /**
     * Log score update.
     */
    public function logScoreUpdate(int $scoreId, array $oldValues, array $newValues, array $metadata = []): void
    {
        $this->logScoring('score_update', $scoreId, $oldValues, $newValues, $metadata);
    }

    /**
     * Log leaderboard update.
     */
    public function logLeaderboardUpdate(int $leaderboardId, array $data, array $metadata = []): void
    {
        $this->logScoring('leaderboard_update', $leaderboardId, null, $data, $metadata);
    }

    /**
     * Log API request.
     */
    public function logApiRequest(string $method, string $path, array $metadata = []): void
    {
        $this->logApi('api_request', array_merge($metadata, [
            'method' => $method,
            'path' => $path,
            'requested_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log API response.
     */
    public function logApiResponse(string $method, string $path, int $statusCode, array $metadata = []): void
    {
        $this->logApi('api_response', array_merge($metadata, [
            'method' => $method,
            'path' => $path,
            'status_code' => $statusCode,
            'responded_at' => now()->toIso8601String(),
        ]));
    }

    /**
     * Log API error.
     */
    public function logApiError(string $method, string $path, string $error, array $metadata = []): void
    {
        $this->logApi('api_error', array_merge($metadata, [
            'method' => $method,
            'path' => $path,
            'error' => $error,
            'occurred_at' => now()->toIso8601String(),
        ]));
    }
}
