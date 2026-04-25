# Guía de Instalación y Configuración

## Requisitos Previos

### Software Requerido
- **PHP**: 8.3 o superior
- **Composer**: 2.x o superior
- **Node.js**: 18.x o superior
- **npm**: 9.x o superior
- **MySQL**: 8.0 o superior (o PostgreSQL 15+)
- **Redis**: 7.x o superior (opcional, para caché)
- **Git**: 2.x o superior

### Extensiones PHP Requeridas
```bash
# Extensiones obligatorias
php -m | grep -E "bcmath|ctype|curl|dom|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml"

# Extensiones recomendadas
php -m | grep -E "redis|imagick|gd|intl|zip"
```

## Instalación Paso a Paso

### 1. Clonar el Repositorio

```bash
# Clonar el proyecto
git clone <url-del-repositorio> proyecto-quiniela
cd proyecto-quiniela

# Verificar rama actual
git branch
```

### 2. Configurar Entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 3. Instalar Dependencias

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias JavaScript
npm install
```

### 4. Configurar Base de Datos

#### Opción A: MySQL (Recomendado para producción)
```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE quiniela_fifa_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'quiniela_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON quiniela_fifa_2026.* TO 'quiniela_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Opción B: SQLite (Desarrollo)
```bash
# Crear archivo de base de datos
touch database/database.sqlite
```

### 5. Configurar Variables de Entorno

Editar archivo `.env`:

```env
# ===========================================
# CONFIGURACIÓN DE LA APLICACIÓN
# ===========================================
APP_NAME="Quiniela FIFA 2026"
APP_ENV=local
APP_KEY=base64:... # Generado automáticamente
APP_DEBUG=true
APP_URL=http://localhost:8000

# ===========================================
# CONFIGURACIÓN DE BASE DE DATOS
# ===========================================

# Opción A: MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiniela_fifa_2026
DB_USERNAME=quiniela_user
DB_PASSWORD=tu_password_seguro

# Opción B: SQLite (comentar MySQL y descomentar esto)
# DB_CONNECTION=sqlite
# DB_DATABASE=/var/www/html/proyecto-quiniela/database/database.sqlite

# ===========================================
# CONFIGURACIÓN DE SESIÓN Y CACHÉ
# ===========================================
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
QUEUE_CONNECTION=database

# ===========================================
# CONFIGURACIÓN DE REDIS (OPCIONAL)
# ===========================================
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ===========================================
# CONFIGURACIÓN DE MAIL
# ===========================================
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@quiniela-fifa2026.com"
MAIL_FROM_NAME="${APP_NAME}"

# Pusher Configuration
PUSHER_APP_KEY=tu-pusher-app-key
PUSHER_APP_SECRET=tu-pusher-app-secret
PUSHER_APP_ID=tu-pusher-app-id
PUSHER_APP_CLUSTER=us2

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost:8000

# ===========================================
# CONFIGURACIÓN DE PUSHER (NOTIFICACIONES)
# ===========================================
PUSHER_APP_KEY=tu-pusher-app-key
PUSHER_APP_SECRET=tu-pusher-app-secret
PUSHER_APP_ID=tu-pusher-app-id
PUSHER_APP_CLUSTER=us2

BROADCAST_DRIVER=pusher
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# ===========================================
# CONFIGURACIÓN DE LOGGING
# ===========================================
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=debug
LOG_DAILY_DAYS=90

# ===========================================
# CONFIGURACIÓN DE SANCTUM
# ===========================================
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### 6. Ejecutar Migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (datos iniciales)
php artisan db:seed
```

### 7. Instalar y Configurar Paquetes

#### 7.1 Laravel Sanctum
```bash
# Instalar Sanctum
composer require laravel/sanctum

# Publicar configuración
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Ejecutar migraciones
php artisan migrate
```

#### 7.2 Laravel Fortify
```bash
# Instalar Fortify
composer require laravel/fortify

# Publicar configuración
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

# Ejecutar migraciones
php artisan migrate

# Instalar Fortify
php artisan fortify:install
```

#### 7.3 Spatie Laravel Permission
```bash
# Instalar Spatie Permission
composer require spatie/laravel-permission

# Publicar configuración y migraciones
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Ejecutar migraciones
php artisan migrate
```

#### 7.4 Pusher PHP Server
```bash
# Instalar Pusher
composer require pusher/pusher-php-server
npm install pusher-js
```

### 8. Configurar Paquetes

#### 8.1 Configurar Sanctum
Editar `config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,localhost:8080,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

#### 8.2 Configurar Fortify
Editar `config/fortify.php`:
```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

#### 8.3 Configurar Spatie Permission
Editar `config/permission.php`:
```php
'guards' => [
    'web' => [
        'provider' => 'users',
    ],
    'api' => [
        'provider' => 'users',
        'driver' => 'sanctum',
    ],
],
```

### 9. Crear Roles y Permisos Iniciales

Crear seeder `database/seeders/RolesAndPermissionsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $admin = Role::create(['name' => 'admin'])
            ->givePermissionTo($permissions);

        $organizer = Role::create(['name' => 'organizador'])
            ->givePermissionTo([
                'manage-quinielas',
                'manage-matches',
                'manage-teams',
                'view-results',
                'view-leaderboard',
            ]);

        $player = Role::create(['name' => 'jugador'])
            ->givePermissionTo([
                'make-predictions',
                'view-results',
                'view-leaderboard',
            ]);
    }
}
```

Ejecutar seeder:
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 10. Configurar Logging de Auditoría

Crear configuración personalizada en `config/logging.php`:

```php
'channels' => [
    // ... canales existentes

    'audit' => [
        'driver' => 'daily',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
        'days' => 90,
    ],

    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 180,
    ],

    'prediction' => [
        'driver' => 'daily',
        'path' => storage_path('logs/prediction.log'),
        'level' => 'info',
        'days' => 60,
    ],

    'scoring' => [
        'driver' => 'daily',
        'path' => storage_path('logs/scoring.log'),
        'level' => 'info',
        'days' => 60,
    ],
],
```

### 11. Configurar Broadcasting

Editar `config/broadcasting.php`:

```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
        ],
    ],
],
```

### 12. Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 13. Iniciar Servicios

```bash
# Iniciar servidor de desarrollo
php artisan serve

# En otra terminal, iniciar Vite
npm run dev

# En otra terminal, iniciar queue worker
php artisan queue:work

# En otra terminal, iniciar logs en tiempo real
php artisan pail
```

## Verificación de Instalación

### 1. Verificar Paquetes Instalados
```bash
# Verificar Sanctum
php artisan tinker
>>> app()->make('Laravel\Sanctum\Sanctum');

# Verificar Fortify
>>> app()->make('Laravel\Fortify\Fortify');

# Verificar Spatie Permission
>>> app()->make('Spatie\Permission\PermissionRegistrar');
```

### 2. Verificar Base de Datos
```bash
# Verificar tablas creadas
php artisan migrate:status

# Verificar roles y permisos
php artisan tinker
>>> \Spatie\Permission\Models\Role::all();
>>> \Spatie\Permission\Models\Permission::all();
```

### 3. Verificar Configuración
```bash
# Verificar configuración de la aplicación
php artisan config:clear
php artisan config:cache

# Verificar rutas
php artisan route:list

# Verificar eventos
php artisan event:list
```

## Solución de Problemas

### Problema 1: Error de Permisos
```bash
# Solución
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Problema 2: Error de Clave
```bash
# Solución
php artisan key:generate
php artisan config:clear
```

### Problema 3: Error de Base de Datos
```bash
# Solución
php artisan migrate:fresh
php artisan db:seed
```

### Problema 4: Error de Paquetes
```bash
# Solución
composer dump-autoload
php artisan package:discover
```

### Problema 5: Error de Vite
```bash
# Solución
rm -rf node_modules package-lock.json
npm install
npm run dev
```

## Comandos Útiles

### Desarrollo
```bash
# Iniciar todo el entorno de desarrollo
composer dev

# Ejecutar pruebas
php artisan test

# Formatear código
./vendor/bin/pint

# Ver logs en tiempo real
php artisan pail

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Base de Datos
```bash
# Crear migración
php artisan make:migration create_table_name

# Ejecutar migraciones
php artisan migrate

# Rollback migraciones
php artisan migrate:rollback

# Seeders
php artisan db:seed
php artisan db:seed --class=SeederName
```

### Modelos
```bash
# Crear modelo
php artisan make:model ModelName -mcr

# -m: crear migración
# -c: crear controlador
# -r: crear resource controller
```

### Controladores
```bash
# Crear controlador
php artisan make:controller ControllerName

# Crear controlador resource
php artisan make:controller ControllerName --resource
```

## Próximos Pasos

1. **Revisar documentación**: Leer `PLAN_IMPLEMENTACION.md` y `ARQUITECTURA.md`
2. **Configurar IDE**: Configurar VSCode o PhpStorm
3. **Configurar Git**: Configurar hooks de pre-commit
4. **Configurar CI/CD**: Configurar GitHub Actions o similar
5. **Comenzar desarrollo**: Seguir el plan de implementación por fases

## Referencias

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Laravel Fortify](https://laravel.com/docs/fortify)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Pusher Documentation](https://pusher.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Vite](https://vitejs.dev/guide/)
