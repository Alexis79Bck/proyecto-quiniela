# Definición de migraciones y tablas

Este documento resume las migraciones principales que definen las tablas del dominio:
equipos, competiciones, temporadas, etapas, grupos, juegos, predicciones, usuarios,
tokens y auditoría. Incluye las columnas, relaciones y notas relevantes.

## `2026_04_14_000003_create_teams_table.php`
Tabla: `equipos`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: nombre del proveedor de datos.
- `nombre`: nombre completo del equipo.
- `nombre_corto`: nombre corto o abreviado del equipo.
- `codigo`: código único de 3 caracteres.
- `emblema_url`: URL del escudo o emblema del equipo.
- `pais`: país del equipo.
- `bandera_url`: URL de la bandera del equipo.
- `anio_fundado`: año de fundación.
- `created_at`, `updated_at`: marcas de tiempo.
- `deleted_at`: marca de eliminación suave (`softDeletes`).

## `2026_04_14_000004_create_stages_table.php`
Tabla: `etapas`

- `id`: clave primaria automática.
- `temporada_id`: relación con `temporadas`.
- `external_id`: identificador de la etapa por proveedor.
- `proveedor`: proveedor de datos.
- `nombre`: nombre de la etapa o fase.
- `orden`: entero para ordenar etapas en la temporada.
- `created_at`, `updated_at`.

## `2026_06_08_174747_create_competitions_table.php`
Tabla: `competiciones`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: nombre del proveedor de datos.
- `nombre`: nombre de la competición o torneo.
- `codigo`: código opcional de la competición.
- `tipo`: tipo de competición.
- `emblema_url`: URL del emblema de la competición.
- `area_nombre`: nombre del área o región de la competición.
- `area_codigo`: código del área o región.
- `es_activa`: indicador de si la competición está activa.
- `slug`: slug único para rutas/URLs.
- `created_at`, `updated_at`, `deleted_at`.

## `2026_06_08_180117_create_seasons_table.php`
Tabla: `temporadas`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: proveedor de datos.
- `competicion_id`: relación con `competiciones`.
- `nombre`: nombre de la temporada.
- `fecha_inicio`, `fecha_fin`: rango de la temporada.
- `esta_activa`: indicador booleano.
- `created_at`, `updated_at`.

## `2026_06_09_154020_create_groups_table.php`
Tabla: `grupos`

- `id`: clave primaria automática.
- `temporada_id`: relación con `temporadas`.
- `nombre`: identificador del grupo (A, B, 1, 2, etc.).
- `created_at`, `updated_at`.

## `2026_06_08_184927_create_seasons_teams_table.php`
Tabla: `equipos_temporadas`

- `id`: clave primaria automática.
- `equipo_id`: relación con `equipos`.
- `temporada_id`: relación con `temporadas`.
- `grupo_id`: relación opcional con `grupos`.
- `created_at`, `updated_at`.
- Índice único/compuesto en `equipo_id, temporada_id` para evitar duplicados.

## `2026_04_14_000005_create_matches_table.php`
Tabla: `juegos`

- `id`: clave primaria automática.
- `external_id`: identificador del juego en proveedor externo.
- `proveedor`: proveedor de datos.
- `temporada_id`, `etapa_id`, `grupo_id`: relaciones con temporada/etapa/grupo.
- `equipo_local_id`, `equipo_visitante_id`: relaciones con `equipos`.
- `jornada`: número de jornada (opcional).
- `fecha_hora`: fecha y hora del partido.
- `fecha_cierre_predicciones`: límite para aceptar/editar predicciones.
- `equipo_ganador_id`: equipo ganador (si aplica).
- Resultados: `equipo_local_goles`, `equipo_visitante_goles`,
  `equipo_local_goles_prorroga`, `equipo_visitante_goles_prorroga`,
  `equipo_local_goles_penales`, `equipo_visitante_goles_penales`.
- `estado`: `programado`, `en_progreso`, `finalizado`.
- `created_at`, `updated_at`.
- Índices: `etapa_id`, `equipo_local_id`, `equipo_visitante_id`, `fecha_hora`, `estado`, `temporada_id`.

## `2026_04_14_000006_create_predictions_table.php`
Tabla: `predicciones`

- `id`: clave primaria automática.
- `uuid`: identificador único universal para la predicción.
- `usuario_id`: relación con `usuarios`.
- `juego_id`: relación con `juegos`.
- `equipo_local_prediccion`, `equipo_visitante_prediccion`: goles previstos.
- `fecha_hora_cierre`: fecha límite para modificar la predicción.
- `puntos_obtenidos`: puntos asignados tras la evaluación.
- `esta_bloqueado`: booleano para bloquear cambios manuales.
- `created_at`, `updated_at`.
- Restricción única: `usuario_id, juego_id`.
- Índices: `usuario_id`, `juego_id`.

## `2026_03_27_000000_create_users_table.php`
Tabla: `usuarios`

- `id`, `nombre_completo`, `nombre_usuario` (único), `correo_electronico` (único),
  `password`, `remember_token`, `created_at`, `updated_at`.
- `sessions`: tabla de sesiones con `id`, `user_id`, `ip_address`, `user_agent`, `payload` y `last_activity`.

## `2026_03_27_172356_create_personal_access_tokens_table.php`
Tabla: `personal_access_tokens`

- `id`: clave primaria.
- `tokenable_type`, `tokenable_id`: relación polimórfica.
- `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`.

## `2026_03_29_000001_create_audit_logs_table.php`
Tabla: `audit_logs`

- Registra eventos y cambios: `usuario_id`, `accion`, `tipo_entidad`, `entity_id`,
  `old_values`, `new_values`, `ip_address`, `user_agent`, `metadata`, `created_at`.
- Índices en `usuario_id`, `accion`, `tipo_entidad/entity_id` y `created_at`.

## Spatie Permission

- Las tablas creadas por `spatie/laravel-permission` (permissions, roles, model_has_permissions,
  model_has_roles, role_has_permissions) manejan la autorización mediante pivotes y claves únicas.

---

Notas:
- Varias tablas incluyen `softDeletes` (`deleted_at`) cuando se necesita borrado lógico.
- Las relaciones con proveedores externos usan `external_id` y `proveedor` para trazabilidad.
- Revise índices y restricciones para optimizar consultas frecuentes (filtrado por fecha,
  equipo, etapa, temporada y estado).
