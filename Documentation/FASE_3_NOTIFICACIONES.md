# Fase 3: Sistema de Alertas y Notificaciones (Persistencia + Polling)

## 📋 Resumen

La Fase 3 implementa un sistema completo de notificaciones persistentes utilizando polling desde el frontend. El sistema permite notificar a los usuarios sobre eventos importantes del sistema de quinielas, almacenando todas las notificaciones en la base de datos para acceso offline y visualización mediante polling.

## 🎯 Objetivos

- ✅ Implementar notificaciones persistentes en base de datos
- ✅ Crear eventos para diferentes tipos de notificaciones
- ✅ Implementar sistema de polling desde el frontend
- ✅ Crear API REST para gestión de notificaciones
- ✅ Implementar frontend para visualización de notificaciones con toasts

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
| `NewQuinielaNotification` | `app/Infrastructure/Notifications/` | database |
| `MatchStartedNotification` | `app/Infrastructure/Notifications/` | database |
| `MatchResultNotification` | `app/Infrastructure/Notifications/` | database |
| `LeaderboardUpdateNotification` | `app/Infrastructure/Notifications/` | database |
| `PredictionReminderNotification` | `app/Infrastructure/Notifications/` | database |
| `WinnersNotification` | `app/Infrastructure/Notifications/` | database |

### 3. Infraestructura

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `SendPusherNotification` | `app/Infrastructure/Notifications/Listeners/` | Listener para enviar notificaciones |
| `create_notifications_table` | `database/migrations/` | Migración para tabla de notificaciones |

### 4. Controlador y Rutas

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `NotificationController` | `app/Http/Controllers/` | Controlador para API de notificaciones |
| `routes/api.php` | `routes/` | Rutas API para notificaciones |

### 5. Frontend

| Componente | Ubicación | Descripción |
|------------|-----------|-------------|
| `NotificationToast.vue` | `resources/js/components/` | Componente de notificación toast |
| `polling.js` | `resources/js/services/` | Servicio de polling para notificaciones |

### 6. Comandos de Prueba

| Comando | Descripción |
|---------|-------------|
| `php artisan notifications:test` | Probar emisión de notificaciones |

## 🔧 Configuración

### Variables de Entorno (.env)

```bash
# Notification Polling Configuration
NOTIFICATION_POLLING_INTERVAL=30000  # Intervalo de polling en ms (30 segundos)
BROADCAST_DRIVER=log  # No se usa broadcasting, solo persistencia
```

### Configuración de Queue (config/queue.php)

Asegurarse de que las colas estén configuradas para procesar notificaciones en background.

##  API REST

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

### Configuración de Polling

El archivo `resources/js/services/polling.js` implementa el polling periódico para consultar notificaciones no leídas.

```javascript
import { pollNotifications } from './polling';

// Iniciar polling de notificaciones
pollNotifications(userId, {
  interval: 30000, // 30 segundos
  onNewNotifications: (notifications) => {
    // Mostrar toasts
    notifications.forEach(notification => {
      showToast(notification.data.message, 'info');
    });
  }
});
```

### Componente NotificationToast

El componente `NotificationToast.vue` muestra notificaciones toast en el frontend mediante polling.

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
3. **Notificación Creada**: Se crea la notificación correspondiente y se guarda en la base de datos
4. **Persistencia**: La notificación queda almacenada en la tabla `notifications`
5. **Polling del Frontend**: El frontend consulta periódicamente la API para notificaciones no leídas
6. **UI Actualizada**: Se muestran las notificaciones como toasts al usuario

## 🚀 Próximos Pasos

- [ ] Implementar servicio de polling en el frontend
- [ ] Probar emisión de eventos y persistencia
- [ ] Probar recepción de notificaciones vía polling
- [ ] Verificar persistencia de notificaciones para usuarios offline
- [ ] Implementar notificaciones por email (opcional)
- [ ] Implementar notificaciones push (PWA)

## 📝 Notas Importantes

1. **Persistencia**: Todas las notificaciones se almacenan en la base de datos, permitiendo acceso offline.

2. **Cola de Trabajos**: Las notificaciones se envían a través de colas para mejorar el rendimiento. Asegúrate de tener un worker de cola ejecutándose:

   ```bash
   php artisan queue:work
   ```

3. **Autenticación**: Los endpoints de la API requieren autenticación con Sanctum.

4. **Polling**: El frontend debe implementar polling periódico para consultar notificaciones no leídas.

## 🐛 Solución de Problemas

### Las notificaciones no se muestran en el frontend

1. Verifica que el polling esté implementado y ejecutándose en el frontend
2. Verifica que el worker de cola esté ejecutándose: `php artisan queue:work`
3. Verifica que las notificaciones se estén guardando en la base de datos
4. Revisa los logs de Laravel para errores en la emisión de eventos

### Las notificaciones no se persisten

1. Verifica que la migración se haya ejecutado: `php artisan migrate:status`
2. Verifica que el usuario tenga el trait `Notifiable`
3. Verifica que las notificaciones implementen `ShouldQueue`

### Polling no funciona

1. Verifica que el intervalo de polling esté configurado correctamente
2. Verifica que las llamadas a la API devuelvan las notificaciones esperadas
3. Revisa la consola del navegador para errores de JavaScript
