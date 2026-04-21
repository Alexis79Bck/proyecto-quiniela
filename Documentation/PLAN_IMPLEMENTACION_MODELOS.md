# Planificación para la Implementación de Modelos, Servicios y Repositorios

## Introducción
Este documento describe los pasos necesarios para implementar los modelos, servicios y repositorios en el proyecto "Quiniela". La implementación se basa en las migraciones y el diseño conceptual de la base de datos. El objetivo es garantizar una arquitectura limpia, modular y alineada con las mejores prácticas de Laravel.

## Estructura General
La estructura del proyecto ya está organizada en directorios específicos para modelos, servicios y repositorios:
- **Modelos**: `app/Models/`
- **Servicios**: `app/Services/`
- **Repositorios**: `app/Repositories/`

## Modelos
Los modelos representan las entidades principales del sistema y se mapearán directamente a las tablas de la base de datos.

### `Usuario`
- Tabla: `usuarios`
- Relaciones:
  - `hasMany` con `predicciones`
  - `hasMany` con `puntajes`
  - `hasMany` con `sesiones`
  - `morphMany` con `notificaciones`
  - `morphMany` con `tokens_personales`
- Métodos adicionales:
  - `calcularPuntajeTotal()`: Calcula el puntaje total del usuario.

### `Grupo`
- Tabla: `groups`
- Relaciones:
  - `hasMany` con `equipos`

### `Equipo`
- Tabla: `teams`
- Relaciones:
  - `belongsTo` con `grupo`
  - `hasMany` con `partidos` como `equipo_local`
  - `hasMany` con `partidos` como `equipo_visitante`

### `Etapa`
- Tabla: `stages`
- Relaciones:
  - `hasMany` con `partidos`

### `Partido`
- Tabla: `matches`
- Relaciones:
  - `belongsTo` con `etapa`
  - `belongsTo` con `equipos` (`equipo_local` y `equipo_visitante`)
  - `hasMany` con `predicciones`
  - `hasMany` con `puntajes`

### `Predicción`
- Tabla: `predictions`
- Relaciones:
  - `belongsTo` con `usuario`
  - `belongsTo` con `partido`

## Servicios
Los servicios encapsulan la lógica de negocio y se implementarán en el directorio `app/Services/`.

### `ServicioPredicciones`
- Funcionalidad:
  - Validar predicciones antes de guardarlas.
  - Calcular puntos obtenidos por una predicción.
- Métodos:
  - `validarPrediccion($usuario, $partido, $datos)`
  - `calcularPuntos($prediccion)`

### `ServicioPartidos`
- Funcionalidad:
  - Gestionar el estado de los partidos.
  - Actualizar resultados y notificar a los usuarios.
- Métodos:
  - `actualizarResultadoPartido($partido, $resultado)`
  - `notificarUsuarios($partido)`

### `ServicioPuntajes`
- Funcionalidad:
  - Calcular y actualizar puntajes de los usuarios.
- Métodos:
  - `actualizarPuntajesUsuarios($partido)`

## Repositorios
Los repositorios gestionan el acceso a los datos y se implementarán en el directorio `app/Repositories/`.

### `RepositorioUsuarios`
- Métodos:
  - `buscarPorCorreo($correo)`
  - `obtenerMejoresPuntajes($limite)`

### `RepositorioPartidos`
- Métodos:
  - `buscarPartidosProximos()`
  - `buscarPartidosPorEtapa($idEtapa)`

### `RepositorioPredicciones`
- Métodos:
  - `buscarPorUsuarioYPartido($idUsuario, $idPartido)`
  - `obtenerPrediccionesPorPartido($idPartido)`

## Pasos de Implementación
1. Crear los modelos en `app/Models/` utilizando `php artisan make:model`.
2. Definir las relaciones y métodos adicionales en cada modelo.
3. Crear los servicios en `app/Services/` y encapsular la lógica de negocio.
4. Crear los repositorios en `app/Repositories/` y definir los métodos de acceso a datos.
5. Escribir pruebas unitarias y de integración para validar la funcionalidad.

## Consideraciones Finales
- Seguir las mejores prácticas de Laravel para la implementación de relaciones, validaciones y servicios.
- Utilizar inyección de dependencias para los servicios y repositorios.
- Documentar cada clase y método con PHPDoc.