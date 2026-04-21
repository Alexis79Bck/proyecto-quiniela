<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'usuario_id',
        'accion',
        'tipo_entidad',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Alcance: Filtrar por acción.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('accion', $action);
    }

    /**
     * Alcance: Filtrar por tipo de entidad.
     */
    public function scopeByEntityType($query, string $entityType)
    {
        return $query->where('tipo_entidad', $entityType);
    }

    /**
     * Alcance: Filtrar por entidad.
     */
    public function scopeByEntity($query, string $entityType, int $entityId)
    {
        return $query->where('tipo_entidad', $entityType)
                     ->where('entity_id', $entityId);
    }

    /**
     * Alcance: Filtrar por usuario.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('usuario_id', $userId);
    }

    /**
     * Alcance: Filtrar por rango de fechas.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Alcance: Filtrar por dirección IP.
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Alcance: Obtener registros recientes (últimos N días).
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Alcance: Filtrar por tipo de acción (auth, quiniela, prediction, scoring, admin, security, api).
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

        return $query->whereIn('accion', $actionPatterns[$actionType]);
    }

    /**
     * Obtener valores antiguos formateados.
     */
    public function getFormattedOldValuesAttribute(): ?string
    {
        if (!$this->old_values) {
            return null;
        }

        return json_encode($this->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtener valores nuevos formateados.
     */
    public function getFormattedNewValuesAttribute(): ?string
    {
        if (!$this->new_values) {
            return null;
        }

        return json_encode($this->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtener metadatos formateados.
     */
    public function getFormattedMetadataAttribute(): ?string
    {
        if (!$this->metadata) {
            return null;
        }

        return json_encode($this->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Verificar si este registro tiene cambios (old_values o new_values).
     */
    public function hasAuditChanges(): bool
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    /**
     * Obtener resumen de cambios.
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
