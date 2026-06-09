# Definición de migraciones y tablas

Este documento describe las migraciones que crean las tablas relacionadas con equipos, competiciones, temporadas, etapas, juegos y predicciones.

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

## `2026_04_14_000004_create_stages_table.php`
Tabla: `etapas`

- `id`: clave primaria automática.
- `nombre`: nombre de la etapa o fase de la competición.
- `created_at`, `updated_at`: marcas de tiempo.

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
- `created_at`, `updated_at`: marcas de tiempo.

## `2026_06_08_180117_create_seasons_table.php`
Tabla: `temporadas`

- `id`: clave primaria automática.
- `external_id`: identificador único del proveedor externo.
- `proveedor`: nombre del proveedor de datos.
- `competicion_id`: relación con `competiciones`.
- `nombre`: nombre de la temporada.
- `fecha_inicio`: fecha de inicio de la temporada.
- `fecha_fin`: fecha de finalización de la temporada.
- `esta_activa`: indicador de si la temporada está activa.
- `created_at`, `updated_at`: marcas de tiempo.

## `2026_06_08_184927_create_seasons_teams_table.php`
Tabla: `equipos_temporadas`

- `id`: clave primaria automática.
- `equipo_id`: relación con `equipos`.
- `temporada_id`: relación con `temporadas`.
- `grupo_id`: relación opcional con `grupos`.
- `created_at`, `updated_at`: marcas de tiempo.

## `2026_04_14_000005_create_matches_table.php`
Tabla: `juegos`

- `id`: clave primaria automática.
- `external_id`: identificador único del juego del proveedor externo.
- `proveedor`: nombre del proveedor de datos.
- `temporada_id`: relación con `temporadas`.
- `etapa_id`: relación con `etapas`.
- `grupo_id`: relación opcional con `grupos`.
- `equipo_local_id`: relación con el equipo local.
- `equipo_visitante_id`: relación con el equipo visitante.
- `jornada`: número de jornada (opcional).
- `fecha_hora`: fecha y hora del partido.
- `fecha_cierre_predicciones`: fecha y hora límite para predicciones.
- `equipo_ganador_id`: equipo ganador, si el juego finalizó.
- `equipo_local_goles`: goles del equipo local en tiempo reglamentario.
- `equipo_visitante_goles`: goles del equipo visitante en tiempo reglamentario.
- `equipo_local_goles_prorroga`: goles del equipo local en prórroga.
- `equipo_visitante_goles_prorroga`: goles del equipo visitante en prórroga.
- `equipo_local_goles_penales`: goles del equipo local en penales.
- `equipo_visitante_goles_penales`: goles del equipo visitante en penales.
- `estado`: estado del partido (`programado`, `en_progreso`, `finalizado`).
- `created_at`, `updated_at`: marcas de tiempo.

## `2026_04_14_000006_create_predictions_table.php`
Tabla: `predicciones`

- `id`: clave primaria automática.
- `uuid`: identificador único universal para la predicción.
- `usuario_id`: relación con `usuarios`.
- `juego_id`: relación con `juegos`.
- `equipo_local_prediccion`: goles previstos para el equipo local.
- `equipo_visitante_prediccion`: goles previstos para el equipo visitante.
- `fecha_hora_cierre`: fecha y hora límite para modificar la predicción.
- `puntos_obtenidos`: puntos obtenidos por la predicción.
- `esta_bloqueado`: indicador si la predicción está bloqueada.
- `created_at`, `updated_at`: marcas de tiempo.

### Restricciones

- `predicciones` tiene una restricción única en `usuario_id` y `juego_id` para evitar duplicados.
- Índices de rendimiento en `usuario_id`, `juego_id`, `etapa_id`, `equipo_local_id`, `equipo_visitante_id`, `fecha_hora` y `estado`.
