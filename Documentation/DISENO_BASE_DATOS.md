# Diseño conceptual de la base de datos

## Visión general
El modelo de datos del proyecto está centrado en una aplicación de quiniela de fútbol que gestiona usuarios, equipos, etapas, partidos, predicciones y puntajes. La base de datos está diseñada con tablas específicas para almacenar: identidad de usuario, organización de grupos y equipos, cronograma de partidos, participaciones de usuarios, resultados de predicciones y puntuación de cada usuario por partido.

Además, incluye soporte para notificaciones, sesiones, tokens de acceso personal y control de permisos/roles mediante la configuración de Laravel Permissions.

---

## Tablas principales

### `users`
- Clave primaria: `id`
- Campos: `fullname`, `username`, `email`, `email_verified_at`, `password`, `total_points`, `remember_token`, `timestamps`
- Propósito: almacena los usuarios registrados en la aplicación y su puntaje total acumulado.
- Relaciones:
  - 1:N con `predictions` (un usuario puede hacer muchas predicciones).
  - 1:N con `scores` (un usuario tiene un registro de puntaje por partido).
  - 1:N con `sessions` (un usuario puede tener múltiples sesiones activas).
  - 1:N con `personal_access_tokens` a través de morphs.
  - 1:N con `notifications` a través de morphs.

### `groups`
- Clave primaria: `id`
- Campos: `name`, `timestamps`
- Propósito: representa grupos de equipos, como los grupos de fase de grupos en un torneo.
- Relaciones:
  - 1:N con `teams` (un grupo puede tener varios equipos).

### `teams`
- Clave primaria: `id`
- Campos: `name`, `fifa_code`, `flag_url`, `group_id`, `timestamps`
- Propósito: almacena los equipos participantes.
- Relaciones:
  - N:1 con `groups` (cada equipo puede pertenecer a un grupo).
  - 1:N con `matches` como `home_team`.
  - 1:N con `matches` como `away_team`.

### `stages`
- Clave primaria: `id`
- Campos: `name`, `order`, `timestamps`
- Propósito: define las etapas del torneo, como fase de grupos, octavos, cuartos, semifinal, final.
- Relaciones:
  - 1:N con `matches` (una etapa agrupa múltiples partidos).

### `matches`
- Clave primaria: `id`
- Campos: `uuid`, `match_number`, `stage_id`, `home_team_id`, `away_team_id`, `match_date`, `home_score`, `away_score`, `status`, `timestamps`
- Propósito: registra cada partido, sus equipos, fecha, resultados y estado.
- Relaciones:
  - N:1 con `stages`.
  - N:1 con `teams` para `home_team_id`.
  - N:1 con `teams` para `away_team_id`.
  - 1:N con `predictions` (un partido puede recibir muchas predicciones de usuarios).
  - 1:N con `scores` (un partido puede generar puntajes para muchos usuarios).

### `predictions`
- Clave primaria: `id`
- Campos: `user_id`, `match_id`, `home_prediction`, `away_prediction`, `points_earned`, `is_locked`, `bonus_enabled`, `timestamps`
- Restricción única: `user_id + match_id`
- Propósito: guarda la predicción de un usuario para un partido.
- Relaciones:
  - N:1 con `users`.
  - N:1 con `matches`.
- Observaciones:
  - `is_locked` indica si la predicción está cerrada para edición.
  - `bonus_enabled` habilita puntuación adicional según reglas de negocio.

### `scores`
- Clave primaria: `id`
- Campos: `user_id`, `match_id`, `exact_results`, `correct_winners`, `correct_team_goals`, `total_goals`, `points_earned`, `timestamps`
- Restricción única: `user_id + match_id`
- Propósito: almacena el detalle de la puntuación de cada usuario en cada partido.
- Relaciones:
  - N:1 con `users`.
  - N:1 con `matches`.
- Observaciones:
  - `points_earned` se calcula con base en la precisión de la predicción.

---

## Soporte de infraestructura y autenticación

### `password_reset_tokens`
- Clave primaria: `email`
- Campos: `token`, `created_at`
- Propósito: gestiona el flujo de restablecimiento de contraseña.

### `sessions`
- Clave primaria: `id`
- Campos: `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`
- Propósito: almacena sesiones de usuario cuando se utiliza el almacenamiento de sesión en base de datos.

### `personal_access_tokens`
- Clave primaria: `id`
- Campos: `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `timestamps`
- Propósito: administra tokens de acceso para autenticar solicitudes de API u otros flujos que requieran tokens personales.

### `notifications`
- Clave primaria: `id` (UUID)
- Campos: `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `timestamps`
- Propósito: almacena notificaciones enviadas a modelos notificables.
- Relaciones:
  - Polimórfica con `users` (u otros modelos notificables) mediante `morphs('notifiable')`.

---

## Control de permisos y roles

El proyecto utiliza tablas generadas por el paquete de permisos de Laravel. Estas tablas pueden variar según la configuración de `config/permission.php`, pero en la migración se definen las siguientes entidades clave:

### `permissions`
- Campos: `id`, `name`, `guard_name`, `timestamps`
- Propósito: define permisos granulares del sistema.

### `roles`
- Campos: `id`, `team_foreign_key` (opcional), `name`, `guard_name`, `timestamps`
- Propósito: agrupa permisos bajo roles.

### `model_has_permissions`
- Campos: `permission_id`, `model_type`, `model_id`, `team_foreign_key` (opcional)
- Propósito: asigna permisos directamente a modelos (`users` o cualquier modelo autorizable).

### `model_has_roles`
- Campos: `role_id`, `model_type`, `model_id`, `team_foreign_key` (opcional)
- Propósito: asigna roles a modelos.

### `role_has_permissions`
- Campos: `permission_id`, `role_id`
- Propósito: vincula permisos a roles.

---

## Principales relaciones del modelo

1. `users` 1 — N `predictions`
2. `users` 1 — N `scores`
3. `matches` 1 — N `predictions`
4. `matches` 1 — N `scores`
5. `stages` 1 — N `matches`
6. `groups` 1 — N `teams`
7. `teams` 1 — N `matches` como local y visitante
8. `users` 1 — N `notifications` (polimórfica)
9. `users` 1 — N `personal_access_tokens` (polimórfica)

---

## Notas conceptuales

- El diseño separa la lógica de predicción (`predictions`) de la lógica de puntaje (`scores`). Esto permite calcular y almacenar resultados de forma independiente y conservar el historial de cada partido.
- El uso de `uuid` en `matches` garantiza identificadores únicos y seguros fuera del contexto de la clave primaria incremental.
- `teams.group_id` es nullable y permite manejar equipos sin grupo asignado, útil para instancias posteriores de torneo o fases eliminatorias.
- El paquete de permisos está preparado para soportar configuraciones con equipos (`teams`) y modelos polimórficos.

---

## Resumen de la topología
- El núcleo del sistema es la relación entre `users`, `matches`, `predictions` y `scores`.
- `stages` organiza la competencia y `groups` organiza equipos para la fase de grupos.
- Las tablas de infraestructura (`notifications`, `sessions`, `personal_access_tokens`, `password_reset_tokens`) dan soporte a la experiencia de usuario y autenticación.
- El esquema de permisos y roles brinda una capa de seguridad y administración de accesos.
