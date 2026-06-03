# SGP — Sistema de Gestión de Proyectos
### Fundación Universitaria Claretiana · UNICLARETIANA

Plataforma web para la gestión, seguimiento y control de proyectos de extensión y proyección social. Desarrollada en Laravel 12 con PostgreSQL, sistema de roles, auditoría completa, exportaciones y gestión documental organizada por tipo.

---

## Características principales

| Módulo | Descripción |
|--------|-------------|
| **Proyectos** | CRUD completo con 5 tipos de documento independientes (proyecto, contrato, presupuesto, cronograma, evidencias) |
| **Banco de Proyectos** | Repositorio de propuestas con flujo de estados, versioning de anexos e historial |
| **Estadísticas** | Dashboard KPI + página analítica con métricas por estado, valor y tendencias |
| **Exportaciones** | PDF, Excel y Word — por proyecto individual o todos los registros |
| **Gestión de usuarios** | CRUD con roles (Admin / Usuario), reset de contraseña, contraseña temporal |
| **Solicitudes de acceso** | Flujo de aprobación/rechazo para nuevos usuarios |
| **Auditoría** | Log completo de operaciones INSERT/UPDATE/DELETE con exportación CSV |
| **Catálogos** | Gestión de programas, tipos de proyecto y líneas de investigación |
| **Seguridad por roles** | Información financiera visible solo para administradores |

---

## Stack tecnológico

- **Backend:** PHP 8.5 · Laravel 12
- **Base de datos:** PostgreSQL (producción) · SQLite en memoria (tests)
- **Frontend:** Blade · Tailwind CSS 4 · Bootstrap 5 · Vite
- **Correo:** Resend (cola asíncrona)
- **Tests:** PHPUnit 11 — 280 tests, 528 assertions

---

## Requisitos

- PHP >= 8.2 con extensiones: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- Node.js + NPM
- PostgreSQL 14+ (o MySQL 8+ compatible)

---

## Instalación local

```bash
# 1. Clonar
git clone https://github.com/Bello2005/gestor.git
cd gestor

# 2. Dependencias PHP y JS
composer install
npm install

# 3. Entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos (.env ya configurado)
php artisan migrate --seed

# 5. Assets
npm run build

# 6. Servidor
php artisan serve
```

Acceder en `http://localhost:8000`

### Variables de entorno mínimas

```env
APP_NAME="SGP UNICLARETIANA"
APP_ENV=local
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestor
DB_USERNAME=postgres
DB_PASSWORD=secret

MAIL_MAILER=resend
RESEND_KEY=re_xxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@uniclaretiana.edu.co
MAIL_FROM_NAME="SGP UNICLARETIANA"
```

---

## Roles y permisos

| Acción | Admin | Usuario |
|--------|-------|---------|
| Ver proyectos | ✅ | ✅ |
| Crear / editar proyectos | ✅ | ✅ |
| Ver valor económico | ✅ | ❌ |
| Gestionar usuarios | ✅ | ❌ |
| Aprobar solicitudes de acceso | ✅ | ❌ |
| Ver auditoría | ✅ | ❌ |
| Gestionar catálogos | ✅ | ❌ |

---

## Gestión documental (por proyecto)

Cada proyecto acepta **5 tipos de documento independientes**, cada uno con su propio botón de carga y eliminación individual:

| Campo | Descripción | Requerido |
|-------|-------------|-----------|
| Archivo del Proyecto | Propuesta, anteproyecto o documento principal | ✅ |
| Contrato o Convenio | Contrato, convenio o acuerdo suscrito | ✅ |
| Presupuesto | Presupuesto detallado o plan financiero | Opcional |
| Cronograma | Cronograma de actividades o plan de trabajo | Opcional |
| Evidencias | Soportes, registros fotográficos u otros (múltiple) | Opcional |

**Límite por archivo:** 20 MB. **Formatos:** PDF, DOC, DOCX, XLS, XLSX, PPT, JPG, PNG.

---

## Tests

```bash
# Suite completa
php artisan test

# Por módulo
php artisan test --filter Auth
php artisan test --filter Proyecto
php artisan test --filter BancoProyecto
php artisan test --filter Admin
```

**Cobertura actual:** 280 tests · 528 assertions · 0 fallos

Los tests usan SQLite en memoria (`.env.testing`) y no requieren conexión a la base de datos de producción.

---

## Despliegue en producción

```bash
# En el servidor
git pull origin production
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Permisos
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Nginx (configuración mínima)

```nginx
server {
    listen 80;
    server_name tudominio.com;
    root /var/www/gestor/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

---

## Seeders disponibles

```bash
php artisan db:seed                    # completo (roles + usuarios + catálogos + demo)
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=UsersSeeder
php artisan db:seed --class=CatalogoSeeder
php artisan db:seed --class=DemoSeeder
```

**Usuarios de demo:**

| Email | Contraseña | Rol |
|-------|-----------|-----|
| `admin@uniclaretiana.edu.co` | `password` | Administrador |
| `usuario@uniclaretiana.edu.co` | `password` | Usuario |

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/     # 18 controladores (Auth, Proyectos, Banco, Admin)
│   ├── Middleware/      # Auth, Admin, AuditAuthentication, VerifyProjectEdit
│   └── Requests/        # StoreProyectoRequest, UpdateProyectoRequest
├── Models/              # 12 modelos Eloquent con Auditable trait
└── Services/
    ├── ProyectoExportService.php   # PDF, Excel, Word
    └── ProyectoFileService.php     # Gestión de 5 tipos de archivo

resources/
├── css/                 # Tailwind 4 + design tokens + componentes
└── views/               # Blade templates (auth, proyectos, banco-proyectos, admin)

tests/
├── Feature/             # 25 archivos — flujos HTTP completos
└── Unit/                # Helpers, modelos, middleware
```

---

## Changelog reciente

- **Gestión documental expandida** — 5 campos independientes por proyecto (proyecto, contrato, presupuesto, cronograma, evidencias)
- **Privacidad financiera** — valor económico oculto para usuarios no administradores
- **Archivos obligatorios** — proyecto y contrato requeridos al crear
- **Suite de tests completa** — 280 tests cubriendo todos los flujos de usuario
- **Fix auditoría** — exportación CSV serializa correctamente valores JSON
- **Fix perfil** — verificación de email redirige a ruta válida

---

## Licencia

Desarrollado para **Fundación Universitaria Claretiana — UNICLARETIANA**.  
Todos los derechos reservados.
