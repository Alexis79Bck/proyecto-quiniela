<div align="center">

# ⚽ Sistema de Quiniela FIFA 2026

### 🏆 Plataforma de Predicciones para el Mundial FIFA 2026

[![PHP Version](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Vite](https://img.shields.io/badge/Vite-8.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)

[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge)]()
[![Code Style](https://img.shields.io/badge/Code_Style-PSR--12-blue?style=for-the-badge)](https://www.php-fig.org/psr/psr-12/)
[![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen?style=for-the-badge)]()

---

**🎯 Aplicación web completa para gestionar predicciones de resultados durante la FIFA Copa Mundial de Fútbol 2026**

[📖 Documentación](#-documentación) • [🚀 Inicio Rápido](#-instalación) • [🏗️ Arquitectura](#️-arquitectura) • [🎮 Puntuación](#-sistema-de-puntuación)

</div>

---

## 📋 Descripción

**Sistema de Quiniela FIFA 2026** es una plataforma web que permite a usuarios pronosticar resultados de partidos del Mundial 2026, acumular puntos según criterios de acierto y competir en clasificaciones en tiempo real.

### 🎮 Concepto del Juego

| Aspecto | Descripción |
|---------|-------------|
| **Evento** | FIFA Copa Mundial de Fútbol 2026 |
| **Mecánica** | Pronosticar resultados de cada encuentro |
| **Puntuación** | Sistema de puntos según aciertos |
| **Clasificación** | Ladder system en tiempo real |
| **Etapas** | Fase de grupos → Brackets (16vos hasta final) |
| **Audiencia** | Grupo limitado (familia/amigos) |

---

## ✨ Características Principales

- 🔐 **Autenticación Robusta** - Sanctum + Fortify con 2FA
- 🛡️ **Control de Acceso Granular** - Spatie Permission
- ⚽ **Sistema de Predicciones** - Pronósticos por partido
- 🏆 **Motor de Puntuación Automática** - Cálculo inteligente
- 📈 **Clasificación en Tiempo Real** - Leaderboard dinámico
- 🔔 **Notificaciones Push** - WebSockets con Pusher
- 📝 **Auditoría Completa** - Logging de todas las acciones
- 🏗️ **Arquitectura DDD** - Escalable y mantenible

---

## 🛠️ Tecnologías

### Backend

| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| ![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white) | 8.3+ | Lenguaje de programación |
| ![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white) | 13.x | Framework web |
| ![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql&logoColor=white) | 8.0+ | Base de datos |
| ![Redis](https://img.shields.io/badge/Redis-7.x-DC382D?logo=redis&logoColor=white) | 7.x | Caché (opcional) |

### Frontend

| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| ![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?logo=tailwindcss&logoColor=white) | 4.x | Framework CSS |
| ![Vite](https://img.shields.io/badge/Vite-8.x-646CFF?logo=vite&logoColor=white) | 8.x | Build tool |

### Paquetes Principales

| Paquete | Función |
|---------|---------|
| 🔐 **Laravel Sanctum** | API tokens y autenticación SPA |
| 🔑 **Laravel Fortify** | Autenticación headless |
| 🛡️ **Spatie Permission** | Roles y permisos granulares |
| 🔔 **Pusher** | WebSockets para tiempo real |

---

## 📦 Requisitos Previos

```bash
✅ PHP 8.3+ | Composer 2.x | Node.js 18.x+ | npm 9.x+
✅ MySQL 8.0+ (o PostgreSQL 15+) | Redis 7.x (opcional) | Git 2.x+
```

### Extensiones PHP Requeridas

```bash
php -m | grep -E "bcmath|ctype|curl|dom|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml"
```

---

## 🚀 Instalación

### 1️⃣ Clonar y Configurar

```bash
git clone <url-del-repositorio> proyecto-quiniela
cd proyecto-quiniela
cp .env.example .env
php artisan key:generate
```

### 2️⃣ Instalar Dependencias

```bash
composer install
npm install
```

### 3️⃣ Configurar Base de Datos

```bash
# MySQL (Recomendado)
mysql -u root -p
CREATE DATABASE quiniela_fifa_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'quiniela_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON quiniela_fifa_2026.* TO 'quiniela_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# O SQLite (Desarrollo)
touch database/database.sqlite
```

### 4️⃣ Migraciones y Paquetes

```bash
php artisan migrate
php artisan db:seed

# Instalar paquetes
composer require laravel/sanctum laravel/fortify spatie/laravel-permission pusher/pusher-php-server
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan fortify:install
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 5️⃣ Iniciar Servicios

```bash
php artisan serve          # Servidor de desarrollo
npm run dev                # Vite (otra terminal)
php artisan queue:work     # Queue worker (otra terminal)
php artisan pail           # Logs en tiempo real (otra terminal)
```

### 🎉 ¡Listo! Accede a `http://localhost:8000`

---

## ⚙️ Configuración

### Variables de Entorno Principales

```env
APP_NAME="Quiniela FIFA 2026"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiniela_fifa_2026
DB_USERNAME=quiniela_user
DB_PASSWORD=tu_password_seguro

PUSHER_APP_KEY=tu-pusher-app-key
PUSHER_APP_SECRET=tu-pusher-app-secret
PUSHER_APP_ID=tu-pusher-app-id
PUSHER_APP_CLUSTER=us2

SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

> 📖 Para más detalles, consulta [GUIA_INSTALACION.md](Documentation/GUIA_INSTALACION.md)

---

## 🏗️ Arquitectura

### Estructura DDD (Domain-Driven Design)

```
app/
├── Domain/              # 🎯 Lógica de negocio
│   ├── Auth/           #   Autenticación
│   ├── User/           #   Usuarios
│   ├── Quiniela/       #   Quinielas
│   ├── Match/          #   Partidos
│   ├── Prediction/     #   Predicciones
│   └── Scoring/        #   Puntuaciones
├── Application/         # ⚙️ Casos de uso
├── Infrastructure/      # 🔧 Servicios externos
├── Presentation/        # 🎨 Controllers y vistas
└── Shared/             # 🔄 Utilidades compartidas
```

### Beneficios

| Beneficio | Descripción |
|-----------|-------------|
| 📦 **Modularidad** | Código organizado por dominios |
| 🔄 **Escalabilidad** | Fácil agregar nuevas funcionalidades |
| 🧪 **Testeabilidad** | Componentes aislados y testeables |
| 🛠️ **Mantenibilidad** | Código limpio y mantenible |

> 📖 Para más detalles, consulta [ARQUITECTURA.md](Documentation/ARQUITECTURA.md)

---

## 🎮 Sistema de Puntuación

### Criterios de Puntuación

| Criterio | Puntos | Descripción |
|----------|--------|-------------|
| 🎯 **Resultado Exacto** | 10 pts | Acertar marcador exacto |
| 🏆 **Ganador Correcto** | 5 pts | Acertar equipo ganador |
| 📊 **Diferencia Correcta** | 3 pts | Acertar diferencia de goles |
| ⚽ **Goles de Equipo** | 2 pts | Acertar goles de un equipo |

### Ejemplo Práctico

**Partido**: Brasil 2 - 1 Argentina

| Predicción | Puntos | Razón |
|------------|--------|-------|
| Brasil 2 - 1 Argentina | **10** | 🎯 Resultado exacto |
| Brasil 3 - 1 Argentina | **5** | 🏆 Ganador correcto |
| Brasil 2 - 0 Argentina | **3** | 📊 Diferencia correcta |
| Brasil 1 - 1 Argentina | **0** | ❌ Sin acierto |

---

## 👥 Roles y Permisos

### Roles del Sistema

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| 👑 **Admin** | Acceso total al sistema | Todos los permisos |
| 📋 **Organizador** | Gestión de quinielas y partidos | Gestión limitada |
| 🎮 **Jugador** | Participación en quinielas | Predicciones y resultados |

### Permisos Granulares

```php
🔐 manage-users          // Gestionar usuarios
📋 manage-quinielas      // Gestionar quinielas
⚽ manage-matches        // Gestionar partidos
🏟️ manage-teams          // Gestionar equipos
🎯 make-predictions      // Realizar predicciones
📊 view-results          // Ver resultados
🏆 view-leaderboard      // Ver clasificación
📝 view-audit-logs       // Ver logs de auditoría
```

---

## 🔔 Sistema de Notificaciones

### Eventos Notificables

| Evento | Descripción |
|--------|-------------|
| 🆕 Nueva quiniela disponible | Cuando se crea una nueva quiniela |
| ⚽ Inicio de partido | Cuando comienza un partido |
| 🏆 Resultado de partido | Cuando termina un partido |
| 📊 Actualización de clasificación | Cambios en el leaderboard |
| ⏰ Recordatorio de predicción | Antes de que cierren las predicciones |
| 🎉 Notificación de ganadores | Anuncio de ganadores |

### Canales de Notificación

| Canal | Descripción |
|-------|-------------|
| 📡 **Broadcast** | Tiempo real (WebSockets) |
| 💾 **Database** | Persistente |
| 📧 **Mail** | Email (opcional) |

---

## 🔒 Seguridad

### Medidas Implementadas

| Medida | Descripción |
|--------|-------------|
| 🔑 **Tokens de API** | Con expiración automática |
| ⏱️ **Rate Limiting** | Protección contra abuso |
| 🛡️ **Protección CSRF** | Seguridad en formularios |
| 🔐 **Autenticación 2FA** | Dos factores disponible |
| 👥 **Roles y Permisos** | Control de acceso granular |
| 📝 **Auditoría** | Acciones críticas registradas |
| 🔒 **Encriptación** | Datos sensibles protegidos |

---

## 📊 Monitoreo y Auditoría

### Canales de Logging

| Canal | Retención | Descripción |
|-------|-----------|-------------|
| 📋 `audit` | 90 días | Acciones críticas |
| 🔒 `security` | 180 días | Intentos de acceso |
| 🎯 `prediction` | 60 días | Predicciones realizadas |
| 🏆 `scoring` | 60 días | Cálculos de puntuación |

### Eventos Auditados

- ✅ Login/logout de usuarios
- ✅ Creación/modificación de quinielas
- ✅ Realización de predicciones
- ✅ Cambios en puntuaciones
- ✅ Acciones administrativas
- ✅ Errores del sistema

---

## 📁 Estructura del Proyecto

```
proyecto-quiniela/
├── 📁 app/
│   ├── 📁 Domain/              # Lógica de negocio
│   ├── 📁 Application/         # Casos de uso
│   ├── 📁 Infrastructure/      # Servicios externos
│   ├── 📁 Presentation/        # Controllers y vistas
│   └── 📁 Shared/              # Utilidades compartidas
├── 📁 database/
│   ├── 📁 migrations/          # Migraciones de BD
│   ├── 📁 seeders/             # Datos iniciales
│   └── 📁 factories/           # Factories para pruebas
├── 📁 resources/
│   ├── 📁 views/               # Vistas Blade
│   ├── 📁 css/                 # Estilos
│   └── 📁 js/                  # JavaScript
├── 📁 routes/
│   ├── 📄 web.php              # Rutas web
│   └── 📄 api.php              # Rutas API
├── 📁 tests/
│   ├── 📁 Unit/                # Pruebas unitarias
│   └── 📁 Feature/             # Pruebas de integración
├── 📁 Documentation/           # Documentación del proyecto
├── 📁 config/                  # Configuraciones
├── 📁 storage/                 # Almacenamiento
└── 📁 public/                  # Archivos públicos
```

---

## 📖 Documentación

| Documento | Descripción | Audiencia |
|-----------|-------------|-----------|
| [📋 **RESUMEN_EJECUTIVO.md**](Documentation/RESUMEN_EJECUTIVO.md) | Visión general del proyecto | Stakeholders, equipo directivo |
| [📝 **PLAN_IMPLEMENTACION.md**](Documentation/PLAN_IMPLEMENTACION.md) | Plan detallado de implementación | Desarrolladores, equipo técnico |
| [🏗️ **ARQUITECTURA.md**](Documentation/ARQUITECTURA.md) | Documentación técnica con diagramas | Arquitectos, desarrolladores senior |
| [🚀 **GUIA_INSTALACION.md**](Documentation/GUIA_INSTALACION.md) | Guía paso a paso de instalación | Desarrolladores, DevOps |
| [✅ **CHECKLIST_IMPLEMENTACION.md**](Documentation/CHECKLIST_IMPLEMENTACION.md) | Lista de verificación de progreso | Líderes de proyecto, QA |
| [📚 **INDICE_DOCUMENTACION.md**](Documentation/INDICE_DOCUMENTACION.md) | Índice de toda la documentación | Todo el equipo |

---


## 🤝 Contribución

¡Las contribuciones son bienvenidas! Para contribuir:

1. 🍴 Fork el proyecto
2. 🌿 Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. 💾 Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. 📤 Push a la rama (`git push origin feature/AmazingFeature`)
5. 🔍 Abre un Pull Request

### Estándares de Código

- ✅ Seguir estándares PSR-12
- ✅ Escribir código limpio y documentado
- ✅ Realizar code reviews
- ✅ Ejecutar pruebas continuamente
- ✅ Mantener documentación actualizada

---

## 📄 Licencia

Este proyecto está licenciado bajo la **Licencia MIT** - ver el archivo [LICENSE](LICENSE) para detalles.

---

## 📞 Soporte

### Para Preguntas Técnicas

1. 📖 Revisar documentación relevante
2. 🚀 Consultar [GUIA_INSTALACION.md](Documentation/GUIA_INSTALACION.md)
3. ✅ Revisar [CHECKLIST_IMPLEMENTACION.md](Documentation/CHECKLIST_IMPLEMENTACION.md)
4. 📧 Contactar líder técnico

### Para Problemas de Configuración

1. 🚀 Seguir [GUIA_INSTALACION.md](Documentation/GUIA_INSTALACION.md) paso a paso
2. 🔍 Revisar solución de problemas
3. ⚙️ Verificar variables de entorno
4. 📝 Consultar logs de error

---

<div align="center">

### 🎉 ¡Comienza a jugar y predice al campeón del Mundo!

**Proyecto**: Sistema de Quiniela FIFA 2026  
**Versión**: 1.0  
**Fecha**: 27 de Marzo de 2026  
**Estado**: 🚀 Listo para implementación  
**Equipo**: 2 desarrolladores fullstack

---

[![Laravel](https://img.shields.io/badge/Built_with-Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

**⚽ Que gane el mejor predictor! ⚽**

</div>
