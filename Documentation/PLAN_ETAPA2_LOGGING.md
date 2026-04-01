# Plan de Implementación - Etapa 2: Sistema de Logging y Auditoría

## Estado Actual

- **config/logging.php**: Configuración estándar de Laravel (canales por defecto)
- **app/Infrastructure/Logging/AuditLogger/**: Directorio vacío
- **Base de datos**: Sin tabla de audit_logs

## Objetivo

Implementar un sistema completo de logging y auditoría que permita:
- Registrar todas las acciones críticas del sistema
- Mantener logs separados por categoría (auditoría, seguridad, predicciones, puntuaciones, API)
- Almacenar logs en archivos y base de datos
- Capturar automáticamente acciones mediante middleware

## Plan de Implementación

### Paso 1: Configurar Canales de Logging Personalizados

**Archivo**: [`config/logging.php`](config/logging.php:1)

**Acciones**:
1. Agregar canal `audit`:
   - Driver: daily
   - Path: storage_path('logs/audit.log')
   - Level: info
   - Days: 90 (retención de 3 meses)

2. Agregar canal `security`:
   - Driver: daily
   - Path: storage_path('logs/security.log')
   - Level: warning
   - Days: 180 (retención de 6 meses)

3. Agregar canal `prediction`:
   - Driver: daily
   - Path: storage_path('logs/prediction.log')
   - Level: info
   - Days: 90

4. Agregar canal `scoring`:
   - Driver: daily
   - Path: storage_path('logs/scoring.log')
   - Level: info
   - Days: 90

5. Agregar canal `api`:
   - Driver: daily
   - Path: storage_path('logs/api.log')
   - Level: info
   - Days: 30

**Resultado esperado**: Archivo config/logging.php actualizado con 5 canales personalizados

---

### Paso 2: Crear Migración para Tabla audit_logs

**Archivo**: `database/migrations/YYYY_MM_DD_HHMMSS_create_audit_logs_table.php`

**Estructura de la tabla**:
```php
- id: bigint, primary key
- user_id: bigint, nullable, foreign key a users
- action: string (ej: login, logout, create_prediction, etc.)
- entity_type: string, nullable (ej: quiniela, match, prediction)
- entity_id: bigint, nullable
- old_values: json, nullable (valores antes del cambio)
- new_values: json, nullable (valores después del cambio)
- ip_address: string, nullable
- user_agent: string, nullable
- metadata: json, nullable (información adicional)
- created_at: timestamp
```

**Índices**:
- user_id
- action
- entity_type + entity_id
- created_at

**Resultado esperado**: Migración lista para ejecutar

---

### Paso 3: Crear Modelo AuditLog

**Archivo**: [`app/Domain/Auth/Models/AuditLog.php`](app/Domain/Auth/Models/AuditLog.php:1)

**Características**:
- Modelo Eloquent con fillable attributes
- Relación belongsTo User
- Métodos de scope para filtrar por acción, entidad, usuario, fecha
- Casting de campos JSON (old_values, new_values, metadata)

**Resultado esperado**: Modelo AuditLog funcional

---

### Paso 4: Crear Servicio AuditLogger

**Archivo**: [`app/Infrastructure/Logging/AuditLogger/AuditLogger.php`](app/Infrastructure/Logging/AuditLogger/AuditLogger.php:1)

**Responsabilidades**:
- Registrar logs de auditoría en archivos (canal audit)
- Registrar logs de auditoría en base de datos (modelo AuditLog)
- Métodos específicos para diferentes tipos de eventos:
  - logAuth(): Eventos de autenticación (login, logout, registro)
  - logQuiniela(): Eventos de quinielas (crear, modificar, eliminar)
  - logPrediction(): Eventos de predicciones (crear, modificar)
  - logScoring(): Eventos de puntuación (calcular, actualizar)
  - logAdmin(): Eventos administrativos
  - logSecurity(): Eventos de seguridad (intentos fallidos, acceso no autorizado)
  - logApi(): Llamadas a API

**Resultado esperado**: Servicio AuditLogger con métodos para todos los tipos de eventos

---

### Paso 5: Crear Evento LogAuditEvent

**Archivo**: [`app/Domain/Auth/Events/LogAuditEvent.php`](app/Domain/Auth/Events/LogAuditEvent.php:1)

**Características**:
- Evento de Laravel que implementa ShouldDispatch
- Propiedades: user_id, action, entity_type, entity_id, old_values, new_values, metadata
- Listener que ejecuta el AuditLogger de forma asíncrona

**Resultado esperado**: Evento y Listener para logging asíncrono

---

### Paso 6: Crear Middleware AuditMiddleware

**Archivo**: [`app/Presentation/Http/Middleware/AuditMiddleware.php`](app/Presentation/Http/Middleware/AuditMiddleware.php:1)

**Responsabilidades**:
- Capturar automáticamente requests HTTP
- Registrar información del request (método, ruta, IP, user agent)
- Identificar usuario autenticado
- Determinar tipo de acción basado en la ruta y método
- Registrar en logs de auditoría

**Configuración**:
- Registrar en `bootstrap/app.php` o `app/Http/Kernel.php`
- Aplicar a rutas que requieren auditoría

**Resultado esperado**: Middleware funcional que captura acciones automáticamente

---

### Paso 7: Crear Helper Functions (Opcional)

**Archivo**: `app/Helpers/audit.php`

**Funciones**:
- audit_log(): Función helper para logging rápido
- audit_auth(): Helper para eventos de autenticación
- audit_action(): Helper para acciones generales

**Resultado esperado**: Funciones helper para facilitar el uso del sistema de logging

---

### Paso 8: Actualizar Variables de Entorno

**Archivo**: [`.env.example`](.env.example:1) y `.env`

**Variables a agregar**:
```env
# Logging
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=debug
LOG_DAILY_DAYS=14

# Audit Logging
AUDIT_LOG_ENABLED=true
AUDIT_LOG_RETENTION_DAYS=90
```

**Resultado esperado**: Variables de entorno configuradas

---

### Paso 9: Crear Seeder de Configuración Inicial (Opcional)

**Archivo**: `database/seeders/AuditConfigSeeder.php`

**Acciones**:
- Configurar canales de logging por defecto
- Crear configuración inicial de auditoría

**Resultado esperado**: Seeder para configuración inicial

---

### Paso 10: Documentación

**Archivo**: `Documentation/LOGGING_GUIDE.md`

**Contenido**:
- Guía de uso del sistema de logging
- Ejemplos de uso de AuditLogger
- Configuración de canales
- Consulta de logs
- Mantenimiento y limpieza de logs

**Resultado esperado**: Documentación completa del sistema

---

## Orden de Ejecución

1. **Paso 1**: Configurar canales de logging (config/logging.php)
2. **Paso 2**: Crear migración de audit_logs
3. **Paso 3**: Crear modelo AuditLog
4. **Paso 4**: Crear servicio AuditLogger
5. **Paso 5**: Crear evento LogAuditEvent
6. **Paso 6**: Crear middleware AuditMiddleware
7. **Paso 7**: Crear helper functions (opcional)
8. **Paso 8**: Actualizar variables de entorno
9. **Paso 9**: Crear seeder (opcional)
10. **Paso 10**: Crear documentación

## Dependencias

- Laravel Framework (ya instalado)
- Monolog (incluido con Laravel)
- Base de datos configurada (SQLite/MySQL)

## Archivos a Crear/Modificar

### Archivos Nuevos:
- `database/migrations/YYYY_MM_DD_HHMMSS_create_audit_logs_table.php`
- `app/Domain/Auth/Models/AuditLog.php`
- `app/Infrastructure/Logging/AuditLogger/AuditLogger.php`
- `app/Domain/Auth/Events/LogAuditEvent.php`
- `app/Domain/Auth/Listeners/AuditLogListener.php`
- `app/Presentation/Http/Middleware/AuditMiddleware.php`
- `app/Helpers/audit.php` (opcional)
- `database/seeders/AuditConfigSeeder.php` (opcional)
- `Documentation/LOGGING_GUIDE.md`

### Archivos a Modificar:
- `config/logging.php` (agregar canales personalizados)
- `.env.example` (agregar variables de logging)
- `.env` (agregar variables de logging)
- `bootstrap/app.php` o `app/Http/Kernel.php` (registrar middleware)

## Validación

### Pruebas Manuales:
1. Verificar que los canales de logging se crean correctamente
2. Ejecutar migración y verificar tabla audit_logs
3. Probar AuditLogger con diferentes tipos de eventos
4. Verificar que el middleware captura requests
5. Revisar archivos de logs generados

### Pruebas Unitarias (Futuro):
- Tests para modelo AuditLog
- Tests para servicio AuditLogger
- Tests para middleware AuditMiddleware
- Tests para eventos y listeners

## Notas Importantes

1. **Rendimiento**: El logging en BD puede afectar el rendimiento. Considerar usar colas para operaciones asíncronas.
2. **Almacenamiento**: Los logs pueden crecer rápidamente. Implementar limpieza automática.
3. **Seguridad**: Los logs pueden contener información sensible. Implementar acceso restringido.
4. **Retención**: Configurar retención adecuada según requisitos legales y de negocio.

## Siguientes Pasos

Una vez completada la Etapa 2, continuar con:
- **Etapa 3**: Notificaciones Pushr
- **Etapa 4**: Estructura DDD y Dominio de Quiniela
