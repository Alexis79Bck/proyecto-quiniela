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

**Sistema de Quiniela FIFA 2026** es una plataforma web diseñada para que los usuarios puedan realizar predicciones de los resultados de los partidos del Mundial FIFA 2026. Los participantes acumulan puntos según sus aciertos y compiten en clasificaciones dinámicas en tiempo real.

### 🎮 Concepto del Juego

| Aspecto | Descripción |
|---------|-------------|
| **Evento** | FIFA Copa Mundial de Fútbol 2026 |
| **Mecánica** | Pronosticar resultados de cada encuentro |
| **Puntuación** | Sistema de puntos según aciertos |
| **Clasificación** | Sistema de clasificación en tiempo real |
| **Etapas** | Fase de grupos → Eliminatorias |
| **Audiencia** | Grupos cerrados (familia/amigos) |

---

## ✨ Características Principales

- 🔐 **Autenticación Segura** - Laravel Sanctum + Fortify con 2FA
- 🛡️ **Control de Acceso Granular** - Spatie Permission
- ⚽ **Sistema de Predicciones** - Pronósticos por partido
- 🏆 **Motor de Puntuación Automática** - Cálculo inteligente
- 📈 **Clasificación en Tiempo Real** - Leaderboard dinámico
- 🔔 **Notificaciones Persistentes** - Polling con base de datos (Opcional)
- 📝 **Auditoría Completa** - Registro de todas las acciones
- 🏗️ **Arquitectura MVC + DDD lite** - Escalable y mantenible

---

## 🛠️ Tecnologías

### Backend

| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| PHP | 8.3+ | Lenguaje de programación |
| Laravel | 13.x | Framework web |
| PostgreSQL | 18.0+ | Base de datos (Recomendado) |
| MySQL | 8.0+ | Base de datos (Opcional) |
| Redis | 7.x | Caché (opcional) |

### Frontend

| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| Tailwind CSS | 4.x | Framework CSS |
| Vite | 8.x | Herramienta de construcción |

### Paquetes Principales

| Paquete | Función |
|---------|---------|
| Laravel Sanctum | API tokens y autenticación SPA |
| Laravel Fortify | Autenticación headless |
| Spatie Permission | Roles y permisos granulares |
| Database Notifications | Notificaciones persistentes (opcional) |

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

### 3️⃣ Migraciones y Paquetes

```bash
php artisan migrate
php artisan db:seed

# Instalar paquetes
composer require laravel/sanctum laravel/fortify spatie/laravel-permission
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan fortify:install
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 4️⃣ Iniciar Servicios

```bash
php artisan serve          # Servidor de desarrollo
npm run dev                # Vite (otra terminal)
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

SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

> 📖 Para más detalles, consulta [GUIA_INSTALACION.md](Documentation/GUIA_INSTALACION.md)

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

## 📄 Licencia

Este proyecto está licenciado bajo la **Licencia MIT** - consulta el archivo [LICENSE](LICENSE) para más detalles.

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
