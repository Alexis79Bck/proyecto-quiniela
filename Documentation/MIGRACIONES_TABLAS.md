# Definición de migraciones y tablas

Este documento describe las migraciones definidas en `database/migrations` y la estructura
actual de las tablas clave del dominio. Incluye las columnas principales, relaciones y notas
relevantes de cada migración.

## `0001_01_01_000000_create_users_table.php`
Tabla: `usuarios`

- `id`: clave primaria automática.
- `nombre_completo`: nombre completo del usuario.
- `nombre_usuario`: nombre de usuario único.
- `correo_electronico`: correo único.
- `password`: contraseña cifrada.
- `remember_token`: token de "recordarme".
- `created_at`, `updated_at`: marcas de tiempo.

Tabla: `sessions`

- `id`: clave primaria de la sesión.
- `user_id`: relación opcional con `usuarios`.
- `ip_address`: dirección IP de la sesión.
- `user_agent`: agente del usuario.
- `payload`: datos de sesión en JSON/binary.
- `last_activity`: marca de tiempo de la última actividad.

## `2026_03_27_172356_create_personal_access_tokens_table.php`
Tabla: `personal_access_tokens`

- `id`: clave primaria automática.
- `tokenable_type`, `tokenable_id`: relación polimórfica al modelo dueño del token.
- `name`: nombre descriptivo del token.
- `token`: token único de 64 caracteres.
- `abilities`: permisos/abilities asignados.
- `last_used_at`: última vez usado.
- `expires_at`: fecha de expiración.
- `created_at`, `updated_at`.

## `2026_03_27_172512_create_permission_tables.php`
Tablas de autorización de Spatie Permission:

- `permissions`: permisos disponibles.
- `roles`: roles disponibles.
- `model_has_permissions`: permisos asignados a modelos.
- `model_has_roles`: roles asignados a modelos.
- `role_has_permissions`: permisos asociados a roles.

Notas:
- Los nombres exactos de las tablas se obtienen desde `config('permission.table_names')`.
- Incluye claves únicas combinadas como `name + guard_name` y pivotes con `model_type`.
- Si `teams` está habilitado, también incluye `team_foreign_key` en algunas tablas.

## `2026_03_29_000001_create_audit_logs_table.php`
Tabla: `audit_logs`

- `id`: clave primaria automática.
- `usuario_id`: relación opcional con `usuarios`.
- `accion`: acción ejecutada.
- `tipo_entidad`: tipo de entidad afectada.
- `entity_id`: identificador de la entidad afectada.
- `old_values`: valores anteriores en JSON.
- `new_values`: valores nuevos en JSON.
- `ip_address`: dirección IP del origen.
- `user_agent`: user agent del actor.
- `metadata`: datos adicionales en JSON.
- `created_at`, `updated_at`.
- Índices en `usuario_id`, `accion`, `tipo_entidad/entity_id` y `created_at`.

## `2026_04_14_000002_create_competitions_table.php`
Tabla: `competiciones`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: proveedor de datos.
- `nombre`: nombre de la competición.
- `codigo`: código opcional de la competición.
- `tipo`: tipo de competición.
- `emblema_url`: URL del emblema.
- `area_nombre`: nombre del área o región.
- `area_codigo`: código del área o región.
- `es_activa`: indica si la competición está activa.
- `slug`: slug único para rutas.
- `created_at`, `updated_at`, `deleted_at`.

## `2026_04_14_000003_create_teams_table.php`
Tabla: `equipos`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: proveedor de datos.
- `nombre`: nombre completo del equipo.
- `nombre_corto`: nombre corto o abreviado.
- `codigo`: código único de 3 caracteres.
- `emblema_url`: URL del emblema.
- `pais`: país del equipo.
- `bandera_url`: URL de la bandera.
- `anio_fundado`: año de fundación.
- `created_at`, `updated_at`, `deleted_at`.

## `2026_04_14_000004_create_seasons_table.php`
Tabla: `temporadas`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: proveedor de datos.
- `competicion_id`: relación con `competiciones`.
- `nombre`: nombre de la temporada.
- `fecha_inicio`: fecha de inicio.
- `fecha_fin`: fecha de fin.
- `esta_activa`: indicador booleano.
- `created_at`, `updated_at`.

## `2026_04_14_000005_create_stages_table.php`
Tabla: `etapas`

- `id`: clave primaria automática.
- `temporada_id`: relación con `temporadas`.
- `external_id`: identificador de la etapa por proveedor.
- `proveedor`: proveedor de datos.
- `nombre`: nombre de la etapa o fase.
- `orden`: orden dentro de la temporada.
- `created_at`, `updated_at`.

## `2026_04_14_000006_create_groups_table.php`
Tabla: `grupos`

- `id`: clave primaria automática.
- `temporada_id`: relación con `temporadas`.
- `nombre`: nombre del grupo (A, B, 1, 2, etc.).
- `created_at`, `updated_at`.

## `2026_04_14_000007_create_matches_table.php`
Tabla: `juegos`

- `id`: clave primaria automática.
- `external_id`: identificador único del juego en el proveedor externo.
- `proveedor`: proveedor de datos.
- `temporada_id`, `etapa_id`, `grupo_id`: relaciones con temporada, etapa y grupo.
- `equipo_local_id`, `equipo_visitante_id`: relaciones con los equipos.
- `jornada`: número de jornada (opcional).
- `fecha_hora`: fecha y hora del partido.
- `fecha_cierre_predicciones`: fecha límite para predicciones.
- `equipo_ganador_id`: equipo ganador cuando aplica.
- `equipo_local_goles`, `equipo_visitante_goles`: resultado en tiempo reglamentario.
- `equipo_local_goles_prorroga`, `equipo_visitante_goles_prorroga`: goles en prórroga.
- `equipo_local_goles_penales`, `equipo_visitante_goles_penales`: goles en penales.
- `estado`: estado del partido (`programado`, `en_progreso`, `finalizado`).
- `created_at`, `updated_at`.
- Índices: `etapa_id`, `grupo_id`, `equipo_local_id`, `equipo_visitante_id`, `fecha_hora`, `estado`, `temporada_id`, y compuestos `etapa_id+grupo_id`, `temporada_id+estado+fecha_hora`.

## `2026_04_14_000008_create_predictions_table.php`
Tabla: `predicciones`

- `id`: clave primaria automática.
- `uuid`: identificador único universal.
- `usuario_id`: relación con `usuarios`.
- `juego_id`: relación con `juegos`.
- `equipo_local_prediccion`: goles previstos local.
- `equipo_visitante_prediccion`: goles previstos visitante.
- `fecha_hora_cierre`: límite de edición.
- `puntos_obtenidos`: puntos asignados.
- `esta_bloqueado`: indicador de bloqueo para cambios.
- `created_at`, `updated_at`.
- Restricción única: `usuario_id + juego_id`.
- Índices: `usuario_id`, `juego_id`.

## `2026_04_14_000009_create_seasons_teams_table.php`
Tabla: `equipos_temporadas`

- `id`: clave primaria automática.
- `equipo_id`: relación con `equipos`.
- `temporada_id`: relación con `temporadas`.
- `grupo_id`: relación opcional con `grupos`.
- `created_at`, `updated_at`.
- Índice compuesto: `equipo_id + temporada_id`.

---

Notas generales:

- Las tablas `competiciones` y `equipos` incluyen `softDeletes` para borrado lógico.
- Las relaciones con proveedores externos usan `external_id` y `proveedor` para trazabilidad.
- `juegos` y `predicciones` agregan índices específicos para consultas sobre etapa, grupo,
  equipos y fechas.
- Las tablas de permisos usan la configuración de `spatie/laravel-permission` y se crean con
  las convenciones de nombres definidas en `config/permission.php`.
