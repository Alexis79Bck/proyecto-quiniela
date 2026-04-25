# Guía del Sistema de Logging y Auditoría

## Resumen

El sistema de logging y auditoría de la Quiniela FIFA 2026 proporciona un registro completo de todas las acciones críticas del sistema, permitiendo trazabilidad, seguridad y análisis de comportamiento.

## Arquitectura del Sistema

### Componentes Principales

1. **Canales de Logging** (config/logging.php)
   - `audit`: Registro de acciones críticas del sistema
   - `security`: Eventos de seguridad y accesos no autorizados
   - `prediction`: Registro de predicciones realizadas
   - `scoring`: Cálculos de puntuación y actualizaciones
   - `api`: Llamadas a la API

2. **Modelo AuditLog** (app/Domain/Auth/Models/AuditLog.php)
   - Almacena logs en base de datos
   - Relación con usuarios
   - Scopes para filtrado avanzado

3. **Servicio AuditLogger** (app/Infrastructure/Logging/AuditLogger/AuditLogger.php)
   - Servicio principal para registrar logs
   - Métodos específicos por tipo de evento
   - Logging dual: archivos + base de datos

4. **Evento LogAuditEvent** (app/Domain/Auth/Events/LogAuditEvent.php)
   - Permite logging asíncrono
   - Desacoplamiento de la lógica de auditoría

5. **Listener AuditLogListener** (app/Domain/Auth/Listeners/AuditLogListener.php)
   - Procesa eventos de auditoría
   - Ejecución en cola para mejor rendimiento

6. **Middleware AuditMiddleware** (app/Http/Middleware/AuditMiddleware.php)
   - Captura automática de requests HTTP
   - Logging de API requests/responses

## Configuración

### EventServiceProvider

El sistema utiliza eventos y listeners para procesar logs de forma asíncrona. El `EventServiceProvider` registra la relación entre `LogAuditEvent` y `AuditLogListener`:

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    LogAuditEvent::class => [
        AuditLogListener::class,
    ],
];
```

Este provider está registrado en `bootstrap/providers.php`.

### Configuración de Colas

El `AuditLogListener` implementa `ShouldQueue` para procesamiento asíncrono. La configuración por defecto:

- **Conexión**: `database`
- **Cola**: `audit`
- **Delay**: 0 segundos

Para procesar los jobs de auditoría, ejecutar:

```bash
php artisan queue:work --queue=audit
```

### Variables de Entorno (.env)

```env
# Logging Configuration
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=debug
LOG_DAILY_DAYS=14

# Audit Logging
AUDIT_LOG_ENABLED=true
AUDIT_LOG_RETENTION_DAYS=90
```

### Canales de Logging

#### Canal `audit`
- **Propósito**: Registro de acciones críticas del sistema
- **Nivel**: info
- **Retención**: 90 días
- **Archivo**: storage/logs/audit.log

#### Canal `security`
- **Propósito**: Eventos de seguridad
- **Nivel**: warning
- **Retención**: 180 días
- **Archivo**: storage/logs/security.log

#### Canal `prediction`
- **Propósito**: Registro de predicciones
- **Nivel**: info
- **Retención**: 90 días
- **Archivo**: storage/logs/prediction.log

#### Canal `scoring`
- **Propósito**: Cálculos de puntuación
- **Nivel**: info
- **Retención**: 90 días
- **Archivo**: storage/logs/scoring.log

#### Canal `api`
- **Propósito**: Llamadas a API
- **Nivel**: info
- **Retención**: 30 días
- **Archivo**: storage/logs/api.log

## Uso del Sistema

### Inyección de Dependencias

```php
use App\Infrastructure\Logging\AuditLogger\AuditLogger;

class MiServicio
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    public function realizarAccion(): void
    {
        // Tu lógica aquí
        $this->auditLogger->logQuinielaCreate($quinielaId, $data);
    }
}
```

### Métodos Disponibles en AuditLogger

#### Autenticación

```php
// Login exitoso
$auditLogger->logLogin($userId, ['ip' => $ip]);

// Logout
$auditLogger->logLogout($userId);

// Registro
$auditLogger->logRegister($userId);

// Login fallido
$auditLogger->logFailedLogin($email, ['reason' => 'invalid_password']);

// Acceso no autorizado
$auditLogger->logUnauthorizedAccess($route, ['method' => $method]);
```

#### Quinielas

```php
// Crear quiniela
$auditLogger->logQuinielaCreate($quinielaId, $data);

// Actualizar quiniela
$auditLogger->logQuinielaUpdate($quinielaId, $oldValues, $newValues);

// Eliminar quiniela
$auditLogger->logQuinielaDelete($quinielaId, $data);
```

#### Predicciones

```php
// Crear predicción
$auditLogger->logPredictionCreate($predictionId, $data);

// Actualizar predicción
$auditLogger->logPredictionUpdate($predictionId, $oldValues, $newValues);

// Eliminar predicción
$auditLogger->logPredictionDelete($predictionId, $data);
```

#### Puntuaciones

```php
// Calcular puntuación
$auditLogger->logScoreCalculate($scoreId, $data);

// Actualizar puntuación
$auditLogger->logScoreUpdate($scoreId, $oldValues, $newValues);

// Actualizar leaderboard
$auditLogger->logLeaderboardUpdate($leaderboardId, $data);
```

#### API

```php
// Request API
$auditLogger->logApiRequest($method, $path, $metadata);

// Response API
$auditLogger->logApiResponse($method, $path, $statusCode, $metadata);

// Error API
$auditLogger->logApiError($method, $path, $error, $metadata);
```

#### Seguridad

```php
// Evento de seguridad
$auditLogger->logSecurity($action, $metadata);
```

### Uso con Eventos

```php
use App\Domain\Auth\Events\LogAuditEvent;

// Disparar evento de auditoría
event(new LogAuditEvent(
    userId: auth()->id(),
    action: 'quiniela_create',
    entityType: 'quiniela',
    entityId: $quinielaId,
    oldValues: null,
    newValues: $data,
    ipAddress: request()->ip(),
    userAgent: request()->userAgent(),
    metadata: ['additional' => 'info'],
    logChannel: 'audit'
));
```

## Base de Datos

### Tabla `audit_logs`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | Identificador único |
| user_id | bigint, nullable | Usuario que realizó la acción |
| action | string | Tipo de acción (login, logout, create_prediction, etc.) |
| entity_type | string, nullable | Tipo de entidad (quiniela, match, prediction, user) |
| entity_id | bigint, nullable | ID de la entidad afectada |
| old_values | json, nullable | Valores antes del cambio |
| new_values | json, nullable | Valores después del cambio |
| ip_address | string, nullable | Dirección IP del usuario |
| user_agent | text, nullable | User agent del navegador |
| metadata | json, nullable | Información adicional |
| created_at | timestamp | Fecha y hora del evento |
| updated_at | timestamp | Fecha y hora de actualización |

### Índices

- `user_id`: Para filtrar por usuario
- `action`: Para filtrar por tipo de acción
- `entity_type + entity_id`: Para filtrar por entidad
- `created_at`: Para filtrar por fecha

## Consultas Comunes

### Obtener logs de un usuario

```php
$logs = AuditLog::byUser($userId)->get();
```

### Obtener logs por tipo de acción

```php
$logs = AuditLog::byAction('login')->get();
```

### Obtener logs de una entidad específica

```php
$logs = AuditLog::byEntity('quiniela', $quinielaId)->get();
```

### Obtener logs por rango de fechas

```php
$logs = AuditLog::byDateRange($startDate, $endDate)->get();
```

### Obtener logs recientes (últimos 30 días)

```php
$logs = AuditLog::recent(30)->get();
```

### Obtener logs por tipo de acción (categoría)

```php
// Logs de autenticación
$authLogs = AuditLog::byActionType('auth')->get();

// Logs de quinielas
$quinielaLogs = AuditLog::byActionType('quiniela')->get();

// Logs de predicciones
$predictionLogs = AuditLog::byActionType('prediction')->get();

// Logs de puntuaciones
$scoringLogs = AuditLog::byActionType('scoring')->get();

// Logs de seguridad
$securityLogs = AuditLog::byActionType('security')->get();

// Logs de API
$apiLogs = AuditLog::byActionType('api')->get();
```

### Obtener logs por dirección IP

```php
$logs = AuditLog::byIpAddress($ipAddress)->get();
```

## Middleware

El `AuditMiddleware` captura automáticamente todos los requests HTTP y registra:

- **API Requests**: Método, ruta, query params, headers
- **API Responses**: Método, ruta, status code, tiempo de respuesta

### Rutas Excluidas

Las siguientes rutas no son auditadas:
- `telescope*`
- `horizon*`
- `pulse*`
- `_ignition*`
- `storage*`

## Mantenimiento

### Limpieza de Logs Antiguos

Los logs se limpian automáticamente según la retención configurada:
- **audit**: 90 días
- **security**: 180 días
- **prediction**: 90 días
- **scoring**: 90 días
- **api**: 30 días

### Comando de Limpieza Manual

```bash
# Limpiar logs más antiguos de X días
php artisan audit:clean --days=90
```

### Monitoreo

Revisar regularmente:
1. Tamaño de archivos de logs en `storage/logs/`
2. Crecimiento de tabla `audit_logs` en base de datos
3. Logs de seguridad para detectar intentos de acceso no autorizado
4. Errores en logs de API para identificar problemas

## Mejores Prácticas

1. **Usar métodos específicos**: Utilizar `logLogin()`, `logQuinielaCreate()`, etc., en lugar del método genérico `log()`
2. **Incluir metadata relevante**: Agregar información adicional que pueda ser útil para debugging
3. **No loggear información sensible**: Evitar contraseñas, tokens, datos de tarjetas de crédito
4. **Revisar logs periódicamente**: Monitorear logs de seguridad y API para detectar anomalías
5. **Mantener retención adecuada**: Ajustar retención según requisitos legales y de negocio

## Troubleshooting

### Los logs no se escriben en archivos

1. Verificar permisos de escritura en `storage/logs/`
2. Verificar configuración de canales en `config/logging.php`
3. Verificar que `LOG_CHANNEL` esté configurado correctamente

### Los logs no se guardan en base de datos

1. Verificar que la migración se haya ejecutado: `php artisan migrate --path=database/migrations/2026_03_29_000001_create_audit_logs_table.php`
2. Verificar conexión a base de datos
3. Revisar logs de error de Laravel

### El middleware no captura requests

1. Verificar que esté registrado en `bootstrap/app.php`
2. Verificar que la ruta no esté en la lista de exclusión
3. Revisar logs de error de Laravel

### El listener no procesa eventos

1. Verificar que la cola esté ejecutándose: `php artisan queue:work`
2. Verificar configuración de colas en `config/queue.php`
3. Revisar jobs fallidos: `php artisan queue:failed`

## Referencias

- [Laravel Logging](https://laravel.com/docs/logging)
- [Monolog Documentation](https://github.com/Seldaek/monolog)
- [Laravel Events](https://laravel.com/docs/events)
- [Laravel Queues](https://laravel.com/docs/queues)
