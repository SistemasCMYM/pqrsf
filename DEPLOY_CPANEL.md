# Despliegue a cPanel (Conexcol) - Laravel 12

Este proyecto ya está listo para producción. Sigue estos pasos en orden.

## 1) Preparar proyecto local

```bash
cd /Users/jorgesanchez/Documents/proyectos/PQRSF
npm run build
```

## 2) Crear base de datos MySQL en cPanel

En cPanel:

- Crea una base de datos (ej: `conexcol_pqrsf`).
- Crea un usuario MySQL y asígnalo a la base.
- Otorga `ALL PRIVILEGES`.

Guarda:

- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_HOST` (normalmente `localhost`)
- `DB_PORT` (normalmente `3306`)

## 3) Subir archivos

Sube el proyecto a una carpeta fuera de `public_html`, por ejemplo:

- `/home/USUARIO/laravel/pqrsf`

No subas carpetas pesadas innecesarias:

- `node_modules`
- `.git`
- `tests` (opcional)

## 4) Configurar Document Root

Debes apuntar el dominio/subdominio a:

- `/home/USUARIO/laravel/pqrsf/public`

Si Conexcol no te deja cambiar Document Root, usa esta alternativa:

1. Deja el proyecto completo en `/home/USUARIO/laravel/pqrsf`.
2. Copia el contenido de `/public` a `public_html`.
3. Edita `public_html/index.php` para que apunte a rutas reales del proyecto:

```php
require __DIR__.'/../laravel/pqrsf/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/pqrsf/bootstrap/app.php';
```

## 5) Crear `.env` de producción

En la raíz del proyecto (no en `public`) crea `.env` con base en `.env.example`.

Mínimo:

```env
APP_NAME="Solicitudes y consultas"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=conexcol_pqrsf
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

## 6) Ejecutar comandos en Terminal de cPanel

Desde la raíz del proyecto:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

## 7) Permisos

Asegura escritura en:

- `storage/`
- `bootstrap/cache/`

Si hace falta:

```bash
chmod -R 775 storage bootstrap/cache
```

## 8) Scheduler (recomendado)

En cPanel > Cron Jobs agrega:

```bash
* * * * * php /home/USUARIO/laravel/pqrsf/artisan schedule:run >> /dev/null 2>&1
```

Esto permitirá ejecutar tareas como vencimiento de PQRSF por SLA.

## 9) Verificación rápida

- Home carga correctamente.
- Login funciona.
- `admin/usuarios` carga.
- `pqrsf/configuracion` carga.
- Importación Excel guarda datos.
- Consulta estado de cuenta devuelve información por cédula.

## 10) Errores comunes

- **Error 500**: revisa `storage/logs/laravel.log`.
- **404 en rutas**: Document Root mal apuntado o `public/.htaccess` no aplicado.
- **No carga logos/adjuntos**: faltó `php artisan storage:link`.
- **Sigue mostrando config vieja**: `php artisan optimize:clear` y luego `php artisan optimize`.
