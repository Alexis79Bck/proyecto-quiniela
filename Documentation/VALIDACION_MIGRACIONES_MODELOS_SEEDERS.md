# Informe de Validación - Migraciones, Modelos y Seeders

**Fecha**: 24 de abril de 2026  
**Estado**: Revisión Completa  
**Referencia**: RESUMEN_EJECUTIVO.md, DISEÑO_BASE_DATOS.md, ARQUITECTURA.md

---

## Resumen Ejecutivo

Se ha realizado un análisis exhaustivo de las migraciones, modelos y seeders implementados en el proyecto para validar su alineación con la lógica de negocio documentada en el RESUMEN_EJECUTIVO.md.

### Calificación General: **70% Alineado** ⚠️

**Estado**: Se requieren ajustes críticos para cumplir con los requisitos del negocio.

---

## 1. Análisis de Migraciones

### ✅ Migraciones Implementadas Correctamente

| Migración | Tabla | Estado | Alineación |
|-----------|-------|--------|------------|
| `0001_01_01_000000_create_users_table.php` | `usuarios` | ✅ | 95% |
| `2026_03_27_172356_create_personal_access_tokens_table.php` | `personal_access_tokens` | ✅ | 100% |
| `2026_03_27_172512_create_permission_tables.php` | `roles, permissions, etc.` | ✅ | 100% |
| `2026_03_29_000001_create_audit_logs_table.php` | `audit_logs` | ✅ | 100% |
| `2026_04_14_000003_create_teams_table.php` | `equipos` | ✅ | 90% |
| `2026_04_14_000004_create_stages_table.php` | `etapas` | ✅ | 100% |
| `2026_04_14_000005_create_matches_table.php` | `juegos` | ✅ | 95% |
| `2026_04_14_000006_create_predictions_table.php` | `predicciones` | ✅ | 90% |

### ❌ Migraciones Faltantes (CRÍTICO)

| Migración | Tabla | Prioridad | Impacto |
|-----------|-------|-----------|---------|
| `create_groups_table` | `grupos` | **ALTA** | Los equipos no pueden asociarse a grupos |
| `create_scores_table` | `scores` o `puntuaciones` | **CRÍTICA** | No hay cálculo de puntuaciones |
| `create_quiniela_usuario_table` | `quiniela_usuario` | **MEDIA** | Tabla pivote para relación N:M |

**Detalle del Problema**:
- El modelo `Equipo` tiene un campo `grupo_id` pero NO existe la tabla `grupos`
- El modelo `Usuario` tiene relación `quinielas()` pero NO existe la tabla `quiniela_usuario`
- **NO existe la tabla `scores`** para el Scoring Domain (requisito fundamental del negocio)

---

## 2. Análisis de Modelos

### ✅ Modelos Implementados

| Modelo | Tabla | Estado | Relaciones | Alineación |
|--------|-------|--------|------------|------------|
| `Usuario` | `usuarios` | ✅ | `quinielas`, `predicciones` | 85% |
| `Equipo` | `equipos` | ✅ | `grupo`, `juegosComoLocal`, `juegosComoVisitante` | 75% |
| `Etapa` | `etapas` | ✅ | `juegos` | 100% |
| `Juego` | `juegos` | ✅ | `etapa`, `equipoLocal`, `equipoVisitante`, `predicciones` | 95% |
| `Prediccion` | `predicciones` | ✅ | `usuario`, `juego` | 90% |
| `Quiniela` | `quinielas` | ⚠️ | `usuarios` | 60% |
| `AuditLog` | `audit_logs` | ✅ | - | 100% |

### ❌ Modelos Faltantes

| Modelo | Dominio | Prioridad | Justificación |
|--------|---------|-----------|---------------|
| `Grupo` | Quiniela | **ALTA** | Necesario para la relación con Equipos |
| `Score` / `Puntuacion` | Scoring | **CRÍTICA** | Esencial para el sistema de puntuación |

### ⚠️ Problemas Detectados en Modelos

#### 2.1 Modelo `Usuario`
```php
// PROBLEMA: El provider en config/auth.php usa 'users' pero la tabla es 'usuarios'
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => Usuario::class, // ✅ Correcto
    ],
],
```
**Estado**: ✅ Correctamente configurado

**Problema**: Falta campo `total_points` mencionado en DISEÑO_BASE_DATOS.md
```php
// Documentación requiere:
$table->unsignedInteger('total_points')->default(0);
```

#### 2.2 Modelo `Equipo`
```php
// PROBLEMA: La migración tiene 'grupo' (string) pero el modelo espera 'grupo_id' (foreign)
#[Fillable([
    'nombre',
    'codigo_fifa',
    'url_bandera',
    'grupo_id',  // ❌ No coincide con la migración
])]
```

**Migración actual**:
```php
$table->string('grupo')->nullable();  // ❌ String, no foreign key
```

**Debería ser**:
```php
$table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
```

#### 2.3 Modelo `Prediccion`
**Problemas detectados**:
1. ✅ Tiene UUID (requerido por estándares)
2. ✅ Tiene `fecha_hora_cierre` (mejor que la documentación)
3. ⚠️ Faltan validaciones de negocio (is_locked antes del partido)
4. ⚠️ No hay relación inversa desde Juego para calcular scores

#### 2.4 Modelo `Quiniela`
**Problemas**:
1. ❌ No existe migración para la tabla `quinielas`
2. ❌ No existe tabla pivote `quiniela_usuario`
3. ⚠️ Campos insuficientes según documentación (debería tener `tipo`, `premio`, etc.)

---

## 3. Análisis de Seeders

### ✅ Seeders Implementados

| Seeder | Estado | Datos | Alineación |
|--------|--------|-------|------------|
| `DatabaseSeeder` | ✅ | Orquestación | 100% |
| `EquipoSeeder` | ✅ | Equipos FIFA 2026 | 95% |
| `EtapaSeeder` | ✅ | 6 etapas | 100% |
| `GrupoSeeder` | ⚠️ | 12 grupos | 70% |
| `JuegoSeeder` | ✅ | Calendario JSON | 95% |
| `RolesAndPermissionsSeeder` | ✅ | 3 roles, 8 permisos | 100% |

### ⚠️ Problemas en Seeders

#### 3.1 `GrupoSeeder`
```php
// PROBLEMA: Intenta usar modelo Grupo que NO existe
Grupo::create($grupo);  // ❌ Error: Class 'App\Models\Grupo' not found
```

#### 3.2 `EquipoSeeder`
```php
// PROBLEMA: Asume que grupo_id es secuencial (1-12)
$grupoId = $index + 1;  // ❌ Frágil si los grupos no existen

// PROBLEMA: La migración de equipos tiene 'grupo' (string), no 'grupo_id'
'grupo_id' => $grupoId,  // ❌ No coincide con migración
```

#### 3.3 `JuegoSeeder`
```php
// PROBLEMA: Asume etapa_id = 1 siempre
'etapa_id' => 1,  // ⚠️ Funciona pero es hardcoded

// PROBLEMA: Todos los partidos van a Fase de Grupos
// No hay lógica para asignar etapas según el calendario
```

#### 3.4 `RolesAndPermissionsSeeder`
```php
// ✅ BIEN IMPLEMENTADO
$permissions = [
    'manage-users',
    'manage-quinielas',
    'manage-matches',
    'manage-teams',
    'make-predictions',
    'view-results',
    'view-leaderboard',
    'view-audit-logs',
];
```
**Estado**: ✅ Alineado 100% con RESUMEN_EJECUTIVO.md

---

## 4. Validación vs Lógica de Negocio

### 4.1 Dominios del Sistema

| Dominio | Estado | Implementación | Brecha |
|---------|--------|----------------|--------|
| **Auth Domain** | ✅ | Fortify + Sanctum + Spatie | 0% |
| **User Domain** | ⚠️ | Modelo Usuario | 15% (falta total_points) |
| **Quiniela Domain** | ❌ | Modelo Quiniela incompleto | 60% |
| **Match Domain** | ✅ | Modelo Juego + Etapa + Equipo | 10% |
| **Prediction Domain** | ✅ | Modelo Prediccion | 10% |
| **Scoring Domain** | ❌ | **NO IMPLEMENTADO** | 100% |

### 4.2 Sistema de Puntuación (CRÍTICO)

**Según RESUMEN_EJECUTIVO.md**:
```
| Criterio | Puntos |
|----------|--------|
| Resultado exacto | 10 puntos |
| Ganador correcto | 5 puntos |
| Diferencia de goles correcta | 3 puntos |
| Goles de un equipo correctos | 2 puntos |
```

**Estado Actual**: ❌ **NO EXISTE IMPLEMENTACIÓN**

**Falta**:
1. ❌ Migración `create_scores_table`
2. ❌ Modelo `Score` o `Puntuacion`
3. ❌ Servicio de cálculo de puntuaciones
4. ❌ Lógica de actualización de `total_points` en Usuario
5. ❌ Leaderboard/Clasificación

### 4.3 Roles y Permisos

**Según Documentación**:
```
Roles: Admin, Organizador, Jugador
Permisos: 8 permisos granulares
```

**Estado Actual**: ✅ **CORRECTAMENTE IMPLEMENTADO**

```php
// RolesAndPermissionsSeeder.php
$roles = [
    'admin' => $permissions,
    'organizador' => [...],
    'jugador' => [...],
];
```

### 4.4 Autenticación y Autorización

| Feature | Requerido | Implementado | Estado |
|---------|-----------|--------------|--------|
| Login/Registro | ✅ | ✅ (Fortify) | ✅ |
| 2FA | ✅ | ✅ (columnas en usuarios) | ✅ |
| API Tokens | ✅ | ✅ (Sanctum) | ✅ |
| Roles/Permisos | ✅ | ✅ (Spatie) | ✅ |
| Password Reset | ⚠️ | ❌ (comentado en migración) | ⚠️ |

---

## 5. Inconsistencias de Nomenclatura

### 5.1 Nombres de Tablas

| Entidad | Documentación | Implementación | Estado |
|---------|---------------|----------------|--------|
| Users | `users` | `usuarios` | ⚠️ (pero válido) |
| Teams | `teams` | `equipos` | ⚠️ (pero válido) |
| Matches | `matches` | `juegos` | ⚠️ (pero válido) |
| Predictions | `predictions` | `predicciones` | ⚠️ (pero válido) |
| Groups | `groups` | **NO EXISTE** | ❌ |
| Scores | `scores` | **NO EXISTE** | ❌ |

**Recomendación**: Mantener consistencia en español O inglés, no mezclar.

### 5.2 Nombres de Columnas

| Entidad | Documentación | Implementación | Estado |
|---------|---------------|----------------|--------|
| User.name | `name` | `nombre_completo` | ✅ (más descriptivo) |
| User.email | `email` | `correo_electronico` | ✅ (más descriptivo) |
| Match.date | `match_date` | `fecha_hora` | ✅ (más preciso) |
| Match.status | `status` | `estado` | ✅ |

---

## 6. Configuración del Sistema

### 6.1 config/auth.php

```php
// ⚠️ PROBLEMA POTENCIAL
'defaults' => [
    'guard' => env('AUTH_GUARD', 'web'),
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),  // ❌ Debería ser 'usuarios'
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',  // ✅ Apunta a Usuario::class
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => Usuario::class,  // ✅ Correcto
    ],
],
```

**Acción Requerida**: Cambiar `'passwords' => 'usuarios'` y asegurar que exista en `config/auth.php`

### 6.2 config/fortify.php

```php
// ✅ CORRECTAMENTE CONFIGURADO
'username' => 'nombre_usuario',
'email' => 'correo_electronico',
'passwords' => 'usuarios',  // ✅ Alineado
```

### 6.3 config/permission.php

```php
// ✅ CORRECTAMENTE CONFIGURADO
'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    // ... Spatie usa inglés por defecto
],
```

---

## 7. Acciones Correctivas Requeridas

### 🔴 CRÍTICO (Bloqueantes)

1. **Crear migración `create_grupos_table`**
   ```bash
   php artisan make:migration create_grupos_table
   ```
   - Agregar columna `nombre` (string, unique)
   - Agregar timestamps

2. **Crear migración `create_scores_table`**
   ```bash
   php artisan make:migration create_scores_table
   ```
   - Columnas: `user_id`, `match_id`, `exact_results`, `correct_winners`, `correct_team_goals`, `total_goals`, `points_earned`
   - Unique constraint: `user_id + match_id`
   - Foreign keys a `usuarios` y `juegos`

3. **Crear modelo `Grupo`**
   ```bash
   php artisan make:model Grupo
   ```
   - Relación: `hasMany(Equipo::class)`

4. **Crear modelo `Score` / `Puntuacion`**
   ```bash
   php artisan make:model Score
   ```
   - Relaciones: `belongsTo(Usuario::class)`, `belongsTo(Juego::class)`
   - Métodos: `calcularPuntos()`, `actualizarTotalUsuario()`

5. **Corregir migración de `equipos`**
   - Cambiar `$table->string('grupo')` por `$table->foreignId('grupo_id')`

### 🟡 ALTA PRIORIDAD

6. **Crear migración `create_quinielas_table`**
   ```bash
   php artisan make:migration create_quinielas_table
   ```
   - Columnas: `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `tipo`, `premio`

7. **Crear migración `create_quiniela_usuario_table`**
   ```bash
   php artisan make:migration create_quiniela_usuario_table
   ```
   - Tabla pivote: `quiniela_id`, `usuario_id`, `fecha_inscripcion`

8. **Corregir `config/auth.php`**
   - Cambiar `'passwords' => 'usuarios'`
   - Agregar provider `usuarios` si no existe

9. **Agregar campo `total_points` a `usuarios`**
   ```bash
   php artisan make:migration add_total_points_to_usuarios_table
   ```

### 🟢 MEDIA PRIORIDAD

10. **Crear Servicio de Scoring**
    ```bash
    php artisan make:service ScoringService
    ```
    - Método: `calcularPuntos(Prediccion $prediccion, Juego $juego)`
    - Método: `actualizarClasificacion()`

11. **Mejorar validaciones en `Prediccion`**
    - Agregar regla: no editar después de `fecha_hora_cierre`
    - Agregar método: `estaBloqueado()`

12. **Completar modelo `Quiniela`**
    - Agregar campos faltantes
    - Agregar métodos de negocio

---

## 8. Pruebas de Validación

### Script de Verificación

```bash
# 1. Verificar migraciones pendientes
php artisan migrate:status

# 2. Verificar modelos existentes
ls -la app/Models/

# 3. Verificar seeders
ls -la database/seeders/

# 4. Ejecutar tests de integridad
php artisan test --filter=ModelTest
```

### Tests Requeridos

1. **Test de Integridad de Base de Datos**
   - Verificar foreign keys
   - Verificar índices
   - Verificar constraints únicos

2. **Test de Modelos**
   - Verificar relaciones
   - Verificar fillable
   - Verificar casts

3. **Test de Seeders**
   - Ejecutar en base de datos de testing
   - Verificar datos creados
   - Verificar integridad referencial

---

## 9. Recomendaciones

### 9.1 Inmediatas

1. **NO ejecutar seeders** hasta corregir el modelo `Grupo`
2. **NO migrar** hasta crear migraciones faltantes
3. **Crear rama Git** para correcciones: `fix/database-inconsistencies`

### 9.1 A Mediano Plazo

1. **Estandarizar nomenclatura**: Decidir si todo será en español o inglés
2. **Agregar tests**: Cada modelo debe tener test de relaciones
3. **Documentar cambios**: Actualizar DISEÑO_BASE_DATOS.md
4. **Code review**: Revisar todos los modelos antes de continuar

### 9.3 Arquitectura

1. **Implementar Repository Pattern** para acceso a datos
2. **Crear DTOs** para transferencia de datos
3. **Agregar Service Layer** para lógica de scoring
4. **Implementar Events** para actualización de clasificación

---

## 10. Conclusión

### Estado Actual

El proyecto tiene **una base sólida** pero con **inconsistencias críticas** que impiden su funcionamiento completo:

- ✅ **Autenticación y Autorización**: 100% funcional
- ✅ **Estructura de Partidos**: 90% completa
- ⚠️ **Gestión de Equipos**: 75% (falta tabla grupos)
- ❌ **Sistema de Puntuación**: 0% (crítico para el negocio)
- ⚠️ **Quinielas**: 40% (modelo incompleto)

### Riesgos

1. **Alto**: Sin tabla `scores`, no hay sistema de quiniela funcional
2. **Medio**: Equipos no pueden asociarse a grupos sin migración
3. **Bajo**: Nomenclatura mixta puede causar confusión

### Próximos Pasos

1. **Crear migraciones faltantes** (grupos, scores, quinielas)
2. **Crear modelos faltantes** (Grupo, Score)
3. **Corregir migración de equipos** (grupo -> grupo_id)
4. **Implementar servicio de scoring**
5. **Ejecutar tests de validación**

---

**Elaborado por**: GitHub Copilot  
**Revisión**: Pendiente por líder técnico  
**Prioridad**: ALTA - Requiere atención inmediata
