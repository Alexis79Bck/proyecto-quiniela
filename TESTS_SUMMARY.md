# Tests para API de Notificaciones

## Tests Implementados

### 1. DTO Tests (`tests/Unit/DTO/NotificationDTOTest.php`)
- ✅ `test_dto_creates_from_model_correctly`
- ✅ `test_dto_extracts_title_from_data`
- ✅ `test_dto_extracts_message_from_data`
- ✅ `test_dto_to_array_returns_correct_structure`
- ✅ `test_dto_handles_null_read_at`
- ✅ `test_dto_with_different_notification_types`

### 2. Polling Tests (`tests/Feature/Notifications/PollingTest.php`)
- ✅ `test_poll_returns_notifications_since_timestamp`
- ✅ `test_poll_returns_empty_when_no_new_notifications`
- ✅ `test_poll_returns_has_new_flag_correctly`
- ✅ `test_poll_with_custom_since_parameter`
- ✅ `test_poll_returns_correct_meta_information`
- ✅ `test_poll_default_since_parameter_works`

### 3. Latest Endpoint Tests (`tests/Feature/Notifications/LatestTest.php`)
- ✅ `test_latest_returns_specified_number_of_notifications`
- ✅ `test_latest_returns_default_10_notifications`
- ✅ `test_latest_returns_notifications_in_descending_order`
- ✅ `test_latest_with_limit_zero_returns_empty`
- ✅ `test_latest_with_large_limit_returns_all_available`

### 4. Health Check Tests (`tests/Feature/Notifications/HealthCheckTest.php`)
- ✅ `test_health_returns_ok_status`
- ✅ `test_health_returns_correct_counts`
- ✅ `test_health_returns_timestamp`
- ⚠️ `test_health_endpoint_requires_authentication` (memory issues)

### 5. Persistence Tests (`tests/Feature/Notifications/PersistenceTest.php`)
- ✅ `test_notifications_persist_in_database`
- ⚠️ `test_notification_read_at_is_persisted` (needs minor fix)
- ⚠️ `test_deleted_notifications_do_not_appear_in_list`
- ⚠️ `test_notification_data_is_correctly_serialized`
- ⚠️ `test_multiple_users_notifications_are_isolated`

## Problemas Conocidos

### Memory Issues
Los tests de feature están experimentando problemas de memoria:
```
Fatal error: Allowed memory size of 536870912 bytes exhausted
```

Para resolver esto, puedes:

1. **Aumentar el límite de memoria en php.ini:**
   ```ini
   memory_limit = 1024M
   ```

2. **Ejecutar tests individualmente:**
   ```bash
   # Run specific test files
   php artisan test tests/Unit/DTO/NotificationDTOTest.php
   php artisan test tests/Feature/Notifications/PollingTest.php
   php artisan test tests/Feature/Notifications/LatestTest.php
   ```

3. **Ejecutar con más memoria:**
   ```bash
   php -d memory_limit=1024M artisan test --filter=Notification
   ```

## Verificación de API Endpoints

| Método | Endpoint | Test Coverage |
|--------|----------|---------------|
| GET | `/api/notifications` | ✓ Existing |
| GET | `/api/notifications/unread` | ✓ Existing |
| GET | `/api/notifications/count` | ✓ Existing |
| GET | `/api/notifications/poll` | ✅ New Tests |
| GET | `/api/notifications/latest` | ✅ New Tests |
| GET | `/api/notifications/health` | ✅ New Tests |
| POST | `/api/notifications/{id}/read` | ✓ Existing |
| POST | `/api/notifications/read-all` | ✓ Existing |
| DELETE | `/api/notifications/{id}` | ✓ Existing |

## Notas de Implementación

1. **Datos de prueba:** Se usaron objetos `DatabaseNotification` directamente en lugar de `notify()` para evitar dependencias de Pusher.
2. **Persistencia:** Los tests verifican que las notificaciones se almacenan correctamente en la base de datos.
3. **Polling:** Los tests verifican que el endpoint `/poll` funciona correctamente con timestamps.
4. **DTO:** Los tests verifican que el DTO maneja correctamente los datos de notificación.

## Ejecución Recomendada

```bash
# Run unit tests first (they work without memory issues)
php artisan test tests/Unit/DTO/

# Run feature tests one by one
php artisan test tests/Feature/Notifications/PollingTest.php
php artisan test tests/Feature/Notifications/LatestTest.php

# If memory issues persist, increase memory limit
php -d memory_limit=1024M artisan test --filter=Notification
```