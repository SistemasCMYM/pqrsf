# Sistema Empresarial PQRSF + Estado de Cuenta SYSO (Laravel)

Proyecto modular en **Laravel 12 + PHP 8 + MySQL/SQLite + Tailwind + Blade** con dos módulos:

1. **Gestión de PQRSF** (formulario público + backoffice con trazabilidad/SLA)
2. **Estado de Cuenta Proyectos SYSO** (consulta autenticada por cédula + importación Excel + base para API)

## Arquitectura propuesta

### Estructura de carpetas

- `app/Http/Controllers/Pqrsf` controladores del módulo PQRSF
- `app/Http/Controllers/EstadoCuenta` controladores del módulo Estado de Cuenta
- `app/Http/Requests` validaciones de formularios y operaciones críticas
- `app/Services/Pqrsf/PqrsfService.php` lógica de negocio PQRSF (radicación, asignación, SLA)
- `app/Services/EstadoCuenta/EstadoCuentaSyncService.php` orquestador de consulta/sincronización
- `app/Services/EstadoCuenta/DataSources/*` patrón de estrategia para fuente de datos
  - `AccountStatementDataSourceInterface`
  - `ExcelAccountStatementDataSource`
  - `ApiAccountStatementDataSource`
- `app/Imports/*` importadores Excel con Laravel Excel
- `app/Jobs/SyncEstadoCuentaFromApiJob.php` job base para sincronización API
- `app/Console/Commands/*` comandos para sincronización y SLA
- `database/migrations/*` esquema completo de datos
- `database/seeders/*` roles, catálogos y datos demo
- `resources/views/*` UI moderna responsive con Tailwind

### Modelo Entidad-Relación (resumen)

- `users` (con campos empresariales y relación a roles)
- `roles / permissions` (Spatie)
- `pqrsf_tipos`, `pqrsf_estados`, `parametrizaciones_sla`
- `pqrsf` (núcleo de casos)
- `pqrsf_adjuntos`, `pqrsf_historial`, `pqrsf_respuestas`
- `estado_cuenta_usuarios` (tabla disponible para escenarios avanzados de mapeo)
- `estado_cuenta_resumen`, `estado_cuenta_detalle`
- `importaciones_excel` (auditoría de cargas)
- `sincronizaciones_api` (auditoría de integraciones)
- `configuraciones_integracion` (fuente activa y credenciales)

## Fases implementadas

### FASE 1: estructura, migraciones y autenticación

- Proyecto Laravel creado desde cero
- Breeze (autenticación) instalado
- Roles/permisos con Spatie
- Migraciones completas para ambos módulos
- Middleware de roles (`ensure.role`)

### FASE 2: módulo PQRSF

- Formulario público sin login (`/pqrsf/registro`)
- Radicado automático
- Validaciones frontend/backend
- Adjuntos seguros
- Estado inicial `Radicada`
- Backoffice con dashboard y bandeja filtrable
- Detalle con trazabilidad, asignación, cambio de estado y respuestas
- Control de SLA (fecha límite + comando `pqrsf:mark-overdue`)

### FASE 3: módulo Estado de Cuenta SYSO

- Consulta autenticada por cédula (`/estado-cuenta`)
- Restricción por usuario vinculado
- Vista con tarjetas resumen + tabla detalle + filtros
- Panel admin para gestión operativa

### FASE 4: importación Excel

- Integración con `maatwebsite/excel`
- Importador multi-hoja (`EstadoCuentaWorkbookImport`)
- Normalización y `upsert` para evitar duplicados
- Registro de importación con estado y errores

### FASE 5: preparación para integración API

- Interfaz desacoplada `AccountStatementDataSourceInterface`
- Implementación API (`ApiAccountStatementDataSource`) con HTTP Client
- Variables `.env` para URL/token/timeout/credenciales
- Job y comando para sincronización manual/futura automatización:
  - `estado-cuenta:sync-api`
  - `SyncEstadoCuentaFromApiJob`

### FASE 6: vistas modernas, dashboard y README

- Home moderna
- Dashboard administrativo
- Formulario público y flujo de éxito
- Dashboards y tablas limpias para ambos módulos
- README técnico de instalación y operación

## Instalación local (macOS / VSCode)

1. Clonar o abrir este proyecto en VSCode.
2. Instalar dependencias PHP:
   ```bash
   composer install
   ```
3. Instalar frontend:
   ```bash
   npm install
   ```
4. Configurar entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Configurar base de datos en `.env` (MySQL recomendado para producción).
6. Ejecutar migraciones y seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```
7. Compilar assets:
   ```bash
   npm run build
   ```
8. Iniciar servidor:
   ```bash
   php artisan serve
   ```
9. Acceder:
   - Público PQRSF: `http://127.0.0.1:8000/pqrsf/registro`
   - Login: `http://127.0.0.1:8000/login`

## Usuarios de prueba

- Administrador: `admin@pqrsf.local` / `password`
- Gestor PQRSF: `analista@pqrsf.local` / `password`
- Asesor: `asesor@pqrsf.local` / `password`

Roles habilitados en el sistema:
- `Administrador`
- `Asesor`
- `Gestor PQRSF`
- `Admin PQRSF`
- `Coordinador Estado Cuenta`

## Comandos útiles

- Marcar PQRSF vencidas:
  ```bash
  php artisan pqrsf:mark-overdue
  ```
- Sincronización API estado de cuenta:
  ```bash
  php artisan estado-cuenta:sync-api
  ```

## Configuración de integración futura por API

Variables soportadas:

- `ACCOUNT_STATEMENT_SOURCE=excel|api`
- `ACCOUNT_STATEMENT_API_BASE_URL=`
- `ACCOUNT_STATEMENT_API_TOKEN=`
- `ACCOUNT_STATEMENT_API_TIMEOUT=15`
- `ACCOUNT_STATEMENT_API_USERNAME=`
- `ACCOUNT_STATEMENT_API_PASSWORD=`

Cambiar a API no requiere rediseñar controladores o vistas; solo la estrategia activa.

## Seguridad y buenas prácticas incluidas

- Autenticación Laravel + verificación
- Middleware por roles
- Policies de acceso
- CSRF activo
- Validaciones con Form Requests
- Registro de trazabilidad completa en PQRSF
- Logs de sincronización/importación

## Estado actual

- Proyecto funcional y ejecutable localmente
- Tests base de Laravel pasando
- Base sólida para continuar desarrollo empresarial y ampliar reglas de negocio
