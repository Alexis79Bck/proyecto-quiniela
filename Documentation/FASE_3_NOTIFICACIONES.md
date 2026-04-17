# Fase 3: Sistema de Alertas y Notificaciones Pusher

## 📋 Resumen

La Fase 3 implementa un sistema completo de notificaciones en tiempo real utilizando Pusher como proveedor de WebSockets. El sistema permite notificar a los usuarios sobre eventos importantes del sistema de quinielas.

## 🎯 Objetivos

- ✅ Implementar notificaciones en tiempo real con Pusher
- ✅ Crear eventos de broadcasting para diferentes tipos de notificaciones
- ✅ Implementar sistema de notificaciones persistente
- ✅ Crear API REST para gestión de notificaciones
- ✅ Implementar frontend para visualización de notificaciones

## 📦 Componentes Implementados

### 1. Eventos de Broadcasting (6 eventos)

| Evento | Ubicación | Descripción |
|--------|-----------|-------------|
| `NewQuinielaAvailable` | `app/Domain/Quiniela/Events/` | Nueva quiniela disponible |
| `MatchStarted` | `app/Domain/Match/Events/` | Partido iniciado |
| `MatchResultAvailable` | `app/Domain/Match/Events/` | Resultado de partido |
| `LeaderboardUpdated` | `app/Domain/Scoring/Events/` | Clasificación actualizada |
| `PredictionReminder` | `app/Domain/Prediction/Events/` | Recordatorio de predicción |
| `WinnersAnnounced` | `app/Domain/Quiniela/Events/` | Ganadores anunciados |

### 2. Notificaciones (6 notificaciones)

| Notificación | Ubicación | Canales |
|--------------|-----------|---------|
| `NewQuinielaNotification` | `app/Infrastructure/Notifications/` | broadcast, database |
| `MatchStartedNotification` | `app/Infrastructure/Notifications/` | broadcast, database |
| `MatchResultNotification` | `app/Infrastructure/Notifications/` | broadcast, database |
| `LeaderboardUpdateNotification` | `app/Infrastructure/Notifications/` | broadcast, database |
| `PredictionReminderNotification` | `app/Infrastructure/Notifications/` | broadcast, database |
| `WinnersNotification` | `app/Infrastructure/Notifications/` | broadcast, database |

### 3. Infraestructura

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `PusherChannel` | `app/Infrastructure/Notifications/Channels/` | Canal personalizado para Pusher |
| `SendPusherNotification` | `app/Infrastructure/Notifications/Listeners/` | Listener para enviar notificaciones |
| `create_notifications_table` | `database/migrations/` | Migración para tabla de notificaciones |

### 4. Controlador y Rutas

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `NotificationController` | `app/Presentation/Http/Controllers/` | Controlador para API de notificaciones |
| `routes/api.php` | `routes/` | Rutas API para notificaciones |

### 5. Frontend

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `echo.js` | `resources/js/` | Configuración de Laravel Echo |
| `NotificationToast.vue` | `resources/js/components/` | Componente de notificación toast |

### 6. Comandos de Prueba

| Comando | Descripción |
|---------|-------------|
| `php artisan notifications:test` | Probar emisión de notificaciones |
| `php artisan pusher:test` | Verificar conexión a Pusher |

## 🔧 Configuración

### Variables de Entorno (.env)

```bash
# Pusher Configuration
PUSHER_APP_KEY=tu-pusher-app-key
PUSHER_APP_SECRET=tu-pusher-app-secret
PUSHER_APP_ID=tu-pusher-app-id
PUSHER_APP_CLUSTER=us2

BROADCAST_DRIVER=pusher
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Configuración de Broadcasting (config/broadcasting.php)

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
],
```

## 📡 Canales de Notificación

### Canales Públicos

| Canal | Eventos | Descripción |
|-------|---------|-------------|
| `quinielas` | `new-quiniela-available`, `winners-announced` | Notificaciones de quinielas |
| `matches` | `match-started`, `match-result-available` | Notificaciones de partidos |
| `leaderboard.{quinielaId}` | `leaderboard-updated` | Actualizaciones de clasificación |

### Canales Privados

| Canal | Eventos | Descripción |
|-------|---------|-------------|
| `user.{userId}` | Todos los eventos | Notificaciones específicas del usuario |

## 🔌 API REST

### Endpoints de Notificaciones

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/notifications` | Obtener todas las notificaciones |
| `GET` | `/api/notifications/unread` | Obtener notificaciones no leídas |
| `GET` | `/api/notifications/count` | Obtener conteo de notificaciones |
| `POST` | `/api/notifications/{id}/read` | Marcar notificación como leída |
| `POST` | `/api/notifications/read-all` | Marcar todas como leídas |
| `DELETE` | `/api/notifications/{id}` | Eliminar notificación |

### Ejemplo de Respuesta

```json
{
  "data": [
    {
      "id": "uuid",
      "type": "App\\Infrastructure\\Notifications\\NewQuinielaNotification",
      "data": {
        "quiniela_id": 1,
        "quiniela_name": "Quiniela Mundial 2026",
        "message": "Nueva quiniela disponible: Quiniela Mundial 2026"
      },
      "read_at": null,
      "created_at": "2026-04-01T12:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

## 🎨 Frontend

### Configuración de Laravel Echo

El archivo `resources/js/echo.js` configura Laravel Echo con Pusher y proporciona funciones para suscribirse a canales de notificaciones.

```javascript
import { subscribeToNotifications, subscribeToLeaderboard } from './echo';

// Suscribirse a notificaciones del usuario
subscribeToNotifications(userId);

// Suscribirse a actualizaciones de leaderboard
subscribeToLeaderboard(quinielaId);
```

### Componente NotificationToast

El componente `NotificationToast.vue` muestra notificaciones toast en el frontend.

```vue
<NotificationToast
  message="Nueva quiniela disponible"
  type="info"
  :duration="5000"
  @close="handleClose"
/>
```

## 🧪 Pruebas

### Probar Notificaciones

```bash
# Probar todas las notificaciones
php artisan notifications:test

# Probar tipo específico
php artisan notifications:test --type=quiniela
php artisan notifications:test --type=match
php artisan notifications:test --type=result
php artisan notifications:test --type=leaderboard
php artisan notifications:test --type=reminder
php artisan notifications:test --type=winners
```

### Verificar Conexión a Pusher

```bash
php artisan pusher:test
```

## 📊 Base de Datos

### Tabla notifications

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | UUID | Identificador único |
| `type` | String | Tipo de notificación |
| `notifiable_type` | String | Tipo de modelo notificable |
| `notifiable_id` | ID | ID del modelo notificable |
| `data` | JSON | Datos de la notificación |
| `read_at` | Timestamp | Fecha de lectura |
| `created_at` | Timestamp | Fecha de creación |
| `updated_at` | Timestamp | Fecha de actualización |

## 🔄 Flujo de Notificaciones

1. **Evento Disparado**: Un evento del dominio es disparado (ej: `NewQuinielaAvailable`)
2. **Listener Ejecutado**: El listener `SendPusherNotification` recibe el evento
3. **Notificación Creada**: Se crea la notificación correspondiente
4. **Envío a Pusher**: La notificación se envía a través del canal `PusherChannel`
5. **Broadcasting**: Pusher transmite la notificación a los clientes suscritos
6. **Frontend Recibe**: El frontend recibe la notificación a través de Laravel Echo
7. **UI Actualizada**: Se muestra la notificación al usuario

## 🚀 Próximos Pasos

- [ ] Configurar credenciales de Pusher en `.env`
- [ ] Probar emisión de eventos
- [ ] Probar recepción de notificaciones en frontend
- [ ] Verificar persistencia de notificaciones
- [ ] Implementar notificaciones por email (opcional)
- [ ] Implementar notificaciones push (PWA)

## 📝 Notas Importantes

1. **Credenciales de Pusher**: Es necesario configurar las credenciales de Pusher en el archivo `.env` para que el sistema funcione correctamente.

2. **Cola de Trabajos**: Las notificaciones se envían a través de colas para mejorar el rendimiento. Asegúrate de tener un worker de cola ejecutándose:

   ```bash
   php artisan queue:work
   ```

3. **Autenticación**: Los endpoints de la API requieren autenticación con Sanctum.

4. **Canales Privados**: Los canales privados requieren autenticación. Laravel Echo automáticamente maneja la autenticación cuando el usuario está autenticado.

## 🐛 Solución de Problemas

### Las notificaciones no se reciben

1. Verifica que las credenciales de Pusher estén configuradas correctamente en `.env`
2. Verifica que el worker de cola esté ejecutándose: `php artisan queue:work`
3. Verifica que el broadcasting esté habilitado: `BROADCAST_DRIVER=pusher`
4. Revisa los logs de Laravel para errores

### Error de conexión a Pusher

1. Verifica que las credenciales de Pusher sean correctas
2. Verifica que el cluster de Pusher sea correcto
3. Verifica que no haya restricciones de firewall

### Las notificaciones no se persisten

1. Verifica que la migración se haya ejecutado: `php artisan migrate:status`
2. Verifica que el usuario tenga el trait `Notifiable`
3. Verifica que las notificaciones implementen `ShouldQueue`
