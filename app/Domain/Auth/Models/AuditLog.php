<?php

namespace App\Domain\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by entity type.
     */
    public function scopeByEntityType($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope: Filter by entity.
     */
    public function scopeByEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId);
    }

    /**
     * Scope: Filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by IP address.
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope: Get recent logs (last N days).
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Filter by action type (auth, quiniela, prediction, scoring, admin, security, api).
     */
    public function scopeByActionType($query, string $actionType)
    {
        $actionPatterns = [
            'auth' => ['login', 'logout', 'register', 'password_reset', 'password_change', 'email_verify', '2fa_enable', '2fa_disable'],
            'quiniela' => ['quiniela_create', 'quiniela_update', 'quiniela_delete', 'quiniela_join', 'quiniela_leave'],
            'prediction' => ['prediction_create', 'prediction_update', 'prediction_delete'],
            'scoring' => ['score_calculate', 'score_update', 'leaderboard_update', 'winner_determine'],
            'admin' => ['user_create', 'user_update', 'user_delete', 'role_assign', 'permission_grant'],
            'security' => ['failed_login', 'unauthorized_access', 'suspicious_activity', 'rate_limit_exceeded'],
            'api' => ['api_request', 'api_response', 'api_error'],
        ];

        if (!isset($actionPatterns[$actionType])) {
            return $query;
        }

        return $query->whereIn('action', $actionPatterns[$actionType]);
    }

    /**
     * Get formatted old values.
     */
    public function getFormattedOldValuesAttribute(): ?string
    {
        if (!$this->old_values) {
            return null;
        }

        return json_encode($this->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get formatted new values.
     */
    public function getFormattedNewValuesAttribute(): ?string
    {
        if (!$this->new_values) {
            return null;
        }

        return json_encode($this->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get formatted metadata.
     */
    public function getFormattedMetadataAttribute(): ?string
    {
        if (!$this->metadata) {
            return null;
        }

        return json_encode($this->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Check if this log has changes (old_values or new_values).
     */
    public function hasAuditChanges(): bool
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    /**
     * Get changes summary.
     */
    public function getChangesSummary(): array
    {
        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }
}
