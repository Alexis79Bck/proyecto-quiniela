# Plan de Implementación - Sistema de Quiniela FIFA Copa Mundial 2026

## Resumen Ejecutivo

Este documento describe el plan detallado para preparar el proyecto Laravel como base fundacional para una aplicación de Quiniela para la FIFA Copa Mundial de Futbol 2026. El proyecto implementará autenticación robusta, control de acceso, logging de auditoría, notificaciones en tiempo real y una arquitectura DDD escalable.

## Estado Actual del Proyecto

- **Framework**: Laravel 13.x
- **PHP**: 8.3+
- **Base de datos**: SQLite (configurable a MySQL/PostgreSQL)
- **Frontend**: Tailwind CSS 4.x + Vite 8.x
- **Autenticación**: Básica (sin paquetes adicionales)
- **Estructura**: MVC tradicional

## Requisitos del Sistema de Quiniela

### Concepto del Juego
- **Evento**: FIFA Copa Mundial de Futbol 2026
- **Mecánica**: Pronosticar resultados de cada encuentro
- **Puntuación**: Usuarios acumulan puntos según criterios de acierto
- **Clasificación**: Sistema Ladder (escalera) para determinar ganadores
- **Etapas**:
  1. Fase de grupos/clasificación
  2. Emparejamientos/brackets (16vos hasta la final)
- **Ganadores**: Top 2-3 por etapa
- **Audiencia**: Grupo limitado de personas (familiar/amigos)

## Arquitectura Propuesta

### 1. Estructura DDD (Domain-Driven Design)

```
app/
├── Domain/
│   ├── Auth/
│   │   ├── Models/
│   │   ├── ValueObjects/
│   │   ├── Events/
│   │   └── Exceptions/
│   ├── User/
│   │   ├── Models/
│   │   ├── ValueObjects/
│   │   ├── Events/
│   │   └── Repositories/
│   ├── Quiniela/
│   │   ├── Models/
│   │   ├── ValueObjects/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   └── Repositories/
│   ├── Match/
│   │   ├── Models/
│   │   ├── ValueObjects/
│   │   └── Events/
│   ├── Prediction/
│   │   ├── Models/
│   │   ├── ValueObjects/
│   │   └── Events/
│   └── Scoring/
│       ├── Models/
│       ├── Services/
│       └── Events/
├── Application/
│   ├── Auth/
│   │   ├── Commands/
│   │   ├── Queries/
│   │   └── DTOs/
│   ├── User/
│   │   ├── Commands/
│   │   ├── Queries/
│   │   └── DTOs/
│   ├── Quiniela/
│   │   ├── Commands/
│   │   ├── Queries/
│   │   └── DTOs/
│   ├── Prediction/
│   │   ├── Commands/
│   │   ├── Queries/
│   │   └── DTOs/
│   └── Scoring/
│       ├── Commands/
│       ├── Queries/
│       └── DTOs/
├── Infrastructure/
│   ├── Auth/
│   │   ├── Sanctum/
│   │   └── Fortify/
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   └── Repositories/
│   ├── Logging/
│   │   └── AuditLogger/
│   ├── Notifications/
│   │   └── Pushr/
│   └── External/
│       └── FIFA-API/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Api/
│   └── Controllers/
├── Console/
│   └── Commands/
└── Shared/
    ├── ValueObjects/
    ├── Events/
    └── Exceptions/
```

### 2. Paquetes a Instalar

#### Autenticación y Autorización
- **Laravel Sanctum**: API tokens y SPA authentication
- **Laravel Fortify**: Autenticación headless (login, registro, 2FA)
- **Spatie Laravel Permission**: Roles y permisos granulares

#### Notificaciones
- **Pushr**: Notificaciones push en tiempo real (WebSockets)

#### Logging y Auditoría
- **Monolog**: Configuración avanzada de logging
- **Custom Audit Logger**: Sistema personalizado de auditoría

#### Utilidades
- **Laravel Pint**: Code style (ya incluido)
- **Laravel Pail**: Logs en tiempo real (ya incluido)

## Plan de Implementación por Fases

### Fase 1: Configuración Base y Autenticación (Días 1-2)

#### 1.1 Instalar Laravel Sanctum
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**Configuración**:
- Configurar guardias de autenticación en `config/auth.php`
- Agregar middleware `sanctum` a rutas API
- Configurar tokens API para usuarios
- Implementar autenticación SPA

#### 1.2 Instalar Laravel Fortify
```bash
composer require laravel/fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan migrate
```

**Configuración**:
- Configurar acciones de autenticación
- Implementar registro de usuarios
- Implementar login/logout
- Implementar recuperación de contraseña
- Implementar verificación de email
- Implementar autenticación de dos factores (2FA)

#### 1.3 Instalar Spatie Laravel Permission
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**Configuración**:
- Crear roles iniciales:
  - `admin`: Acceso total al sistema
  - `organizador`: Gestión de quinielas y usuarios
  - `jugador`: Participación en quinielas
- Crear permisos granulares:
  - `manage-users`: Gestionar usuarios
  - `manage-quinielas`: Gestionar quinielas
  - `make-predictions`: Realizar predicciones
  - `view-results`: Ver resultados
  - `view-leaderboard`: Ver clasificación
- Asignar roles al modelo User

### Fase 2: Logging y Auditoría (Día 3)

#### 2.1 Configurar Sistema de Logging
**Archivo**: `config/logging.php`

**Canales de logging**:
- `audit`: Log de auditoría (acciones críticas)
- `security`: Log de seguridad (intentos de acceso, errores)
- `prediction`: Log de predicciones realizadas
- `scoring`: Log de cálculos de puntuación
- `api`: Log de llamadas API

**Configuración**:
```php
'channels' => [
    'audit' => [
        'driver' => 'daily',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
        'days' => 90,
    ],
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 180,
    ],
    // ... más canales
]
```

#### 2.2 Implementar Audit Logger
**Ubicación**: `app/Infrastructure/Logging/AuditLogger/`

**Componentes**:
- `AuditLogger`: Servicio principal de logging
- `AuditLog`: Modelo Eloquent para logs en BD
- `LogAuditEvent`: Evento para logging asíncrono
- Middleware para capturar acciones automáticamente

**Eventos a auditar**:
- Login/logout de usuarios
- Creación/modificación de quinielas
- Realización de predicciones
- Cambios en puntuaciones
- Acciones administrativas
- Errores del sistema

### Fase 3: Notificaciones Pushr (Día 4)

#### 3.1 Instalar y Configurar Pushr
```bash
composer require pusher/pusher-php-server
```

**Configuración**: `config/broadcasting.php`
```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
]
```

#### 3.2 Implementar Sistema de Notificaciones
**Eventos a notificar**:
- Nueva quiniela disponible
- Inicio de partido
- Resultado de partido
- Actualización de clasificación
- Recordatorio de predicción pendiente
- Notificación de ganadores

**Canales de notificación**:
- `broadcast`: Notificaciones en tiempo real
- `database`: Notificaciones persistentes
- `mail`: Notificaciones por email (opcional)

### Fase 4: Estructura DDD y Dominio de Quiniela (Días 5-7)

#### 4.1 Crear Migraciones

**Usuarios y Autenticación**:
- `users` (ya existe)
- `personal_access_tokens` (Sanctum)
- `password_reset_tokens` (ya existe)
- `sessions` (ya existe)
- `roles` (Spatie)
- `permissions` (Spatie)
- `model_has_roles` (Spatie)
- `model_has_permissions` (Spatie)
- `role_has_permissions` (Spatie)

**Dominio de Quiniela**:
- `quinielas`: Información de quinielas
- `teams`: Equipos participantes
- `matches`: Partidos del torneo
- `predictions`: Predicciones de usuarios
- `scores`: Puntuaciones calculadas
- `leaderboards`: Clasificaciones
- `stages`: Etapas del torneo (grupos, brackets)
- `groups`: Grupos de la fase inicial

**Auditoría**:
- `audit_logs`: Logs de auditoría en BD

#### 4.2 Crear Modelos y Relaciones

**Modelos del Dominio**:
- `User` (actualizar)
- `Quiniela`
- `Team`
- `Match`
- `Prediction`
- `Score`
- `Leaderboard`
- `Stage`
- `Group`
- `AuditLog`

**Relaciones**:
- User -> hasMany -> Predictions
- User -> hasMany -> Scores
- Quiniela -> hasMany -> Matches
- Quiniela -> hasMany -> Users (participantes)
- Match -> belongsTo -> Team (home, away)
- Match -> hasMany -> Predictions
- Prediction -> belongsTo -> User
- Prediction -> belongsTo -> Match
- Score -> belongsTo -> User
- Score -> belongsTo -> Quiniela

#### 4.3 Crear Value Objects
- `Score`: Puntuación (int)
- `PredictionResult`: Resultado predicho (home_score, away_score)
- `MatchResult`: Resultado real (home_score, away_score)
- `Position`: Posición en clasificación
- `StageType`: Tipo de etapa (group, knockout)
- `MatchStatus`: Estado del partido (scheduled, live, finished)

### Fase 5: Lógica de Negocio (Días 8-10)

#### 5.1 Servicios de Puntuación
**Ubicación**: `app/Domain/Scoring/Services/`

**Servicios**:
- `PredictionScoringService`: Calcula puntos por predicción
- `LeaderboardService`: Calcula y actualiza clasificaciones
- `WinnerService`: Determina ganadores por etapa

**Criterios de Puntuación** (ejemplo):
- Resultado exacto: 10 puntos
- Ganador correcto: 5 puntos
- Diferencia de goles correcta: 3 puntos
- Goles de un equipo correctos: 2 puntos

#### 5.2 Servicios de Quiniela
**Ubicación**: `app/Domain/Quiniela/Services/`

**Servicios**:
- `QuinielaService`: Gestión de quinielas
- `MatchService`: Gestión de partidos
- `PredictionService`: Gestión de predicciones
- `TeamService`: Gestión de equipos

### Fase 6: API y Controladores (Días 11-13)

#### 6.1 API REST
**Endpoints**:
- `POST /api/auth/login`: Login
- `POST /api/auth/register`: Registro
- `POST /api/auth/logout`: Logout
- `GET /api/quinielas`: Listar quinielas
- `GET /api/quinielas/{id}`: Detalle de quiniela
- `POST /api/predictions`: Crear predicción
- `GET /api/predictions`: Listar predicciones del usuario
- `GET /api/leaderboard/{quinielaId}`: Ver clasificación
- `GET /api/matches/{id}`: Detalle de partido

#### 6.2 Controladores Web
**Ubicación**: `app/Http/Controllers/`

**Controladores**:
- `AuthController`: Autenticación
- `QuinielaController`: Gestión de quinielas
- `PredictionController`: Gestión de predicciones
- `LeaderboardController`: Visualización de clasificación
- `MatchController`: Visualización de partidos
- `ProfileController`: Gestión de perfil

### Fase 7: Frontend y Vistas (Días 14-16)

#### 7.1 Vistas Blade
**Ubicación**: `resources/views/`

**Vistas**:
- `auth/`: Login, registro, recuperación de contraseña
- `quinielas/`: Listado, detalle, creación
- `predictions/`: Formulario de predicciones
- `leaderboard/`: Clasificación
- `matches/`: Listado y detalle de partidos
- `profile/`: Perfil de usuario
- `admin/`: Panel administrativo

#### 7.2 Componentes UI
**Ubicación**: `resources/views/components/`

**Componentes**:
- `match-card`: Tarjeta de partido
- `prediction-form`: Formulario de predicción
- `leaderboard-table`: Tabla de clasificación
- `score-badge`: Badge de puntuación
- `notification-toast`: Notificación toast

### Fase 8: Pruebas y Documentación (Días 17-18)

#### 8.1 Pruebas Unitarias
**Ubicación**: `tests/Unit/`

**Pruebas**:
- Modelos y relaciones
- Servicios de puntuación
- Value Objects
- Eventos

#### 8.2 Pruebas de Integración
**Ubicación**: `tests/Feature/`

**Pruebas**:
- Autenticación completa
- Flujo de predicciones
- Cálculo de puntuaciones
- API endpoints

#### 8.3 Documentación
- README.md actualizado
- Guía de instalación
- Documentación de API
- Guía de desarrollo

## Configuración de Entorno

### Variables de Entorno (.env)
```env
# Aplicación
APP_NAME="Quiniela FIFA 2026"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiniela_fifa_2026
DB_USERNAME=root
DB_PASSWORD=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:8000

# Pusher
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_ID=your-pusher-id
PUSHER_APP_CLUSTER=us2

# Logging
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=debug

# Mail (opcional)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

## Consideraciones de Seguridad

### Autenticación
- Tokens de API con expiración
- Rate limiting en endpoints de autenticación
- Protección CSRF
- Validación de entrada robusta

### Autorización
- Roles y permisos granulares
- Middleware de autorización en rutas
- Políticas de acceso a recursos

### Datos
- Encriptación de datos sensibles
- Validación de predicciones (no permitir cambios después del partido)
- Auditoría de todas las acciones críticas

## Escalabilidad y Mantenimiento

### Arquitectura
- Separación clara de responsabilidades (DDD)
- Inyección de dependencias
- Eventos y listeners para desacoplamiento
- Repositorios para abstracción de datos

### Performance
- Caché de clasificaciones
- Lazy loading de relaciones
- Índices de base de datos optimizados
- Colas para procesamiento asíncrono

### Monitoreo
- Logs estructurados
- Métricas de uso
- Alertas de errores
- Dashboard de auditoría

## Próximos Pasos

1. **Revisión del plan**: Validar con el equipo de desarrollo
2. **Configuración de entorno**: Preparar entornos de desarrollo
3. **Implementación por fases**: Seguir el orden establecido
4. **Code review**: Revisar cada implementación
5. **Testing**: Ejecutar pruebas continuamente
6. **Documentación**: Mantener documentación actualizada

## Referencias

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel Fortify Documentation](https://laravel.com/docs/fortify)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Pusher Documentation](https://pusher.com/docs)
- [Domain-Driven Design](https://www.domainlanguage.com/ddd/)
