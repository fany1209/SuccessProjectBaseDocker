# Success Project Base

Sistema de gestión empresarial construido con **Laravel 12**, **AdminLTE**, **Jetstream** y **MariaDB**.

---

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (v4.0 o superior)
- [Git](https://git-scm.com/downloads)

> **Nota:** No necesitas instalar PHP, Composer, Node.js, MySQL ni ningún otro software. Docker se encarga de todo.

### 🐧 Configuración para Windows (WSL 2)

Si usas Windows, te recomendamos usar WSL 2 (Windows Subsystem for Linux) para un rendimiento óptimo:

1. Clona este proyecto **dentro del sistema de archivos de WSL** (por ejemplo, en `~/successProjectBase`), y **NO** en el sistema de archivos de Windows (como `C:\Users\...`), ya que Docker funciona mucho más rápido en el entorno de Linux.
2. Abre **Docker Desktop** en tu Windows.
3. Ve a **Settings** (el icono de engranaje) > **Resources** > **WSL Integration**.
4. Asegúrate de tener marcada la opción **"Enable integration with my default WSL distro"**.
5. En la misma pantalla, debajo de "Enable integration with additional distros", enciende el interruptor de tu distribución específica (por ejemplo, `Ubuntu`).
6. Haz clic en **Apply & restart**.

---

## 🚀 Instalación desde Cero

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> successProjectBase
cd successProjectBase
```

### 2. Configurar las variables de entorno

Copia el archivo de entorno para Docker:

```bash
# Windows (PowerShell)
copy .env.docker .env

# Linux / macOS
cp .env.docker .env
```

Abre `.env` y configura tus credenciales reales:

```dotenv
# ── Correo (Gmail SMTP) ────────────────────────
MAIL_USERNAME=tu-correo@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_FROM_ADDRESS="tu-correo@gmail.com"

# ── Pusher (Broadcasting en tiempo real) ────────
PUSHER_APP_ID=tu-pusher-app-id
PUSHER_APP_KEY=tu-pusher-app-key
PUSHER_APP_SECRET=tu-pusher-app-secret
PUSHER_APP_CLUSTER=us2
```

> **💡 Tip:** Para obtener una contraseña de aplicación de Gmail, ve a [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords).

### 3. Colocar el dump de la base de datos

Si tienes un dump SQL de la base de datos, colócalo en la **raíz del proyecto** con el nombre `success.sql`:

```
successProjectBase/
├── success.sql       ← dump aquí
├── docker-compose.yml
├── Dockerfile
└── ...
```

> **⚠️ Importante:** MariaDB importa este archivo **solo la primera vez** que se crea el volumen. Si necesitas re-importar, primero destruye los volúmenes con `docker compose down -v`.

### 4. Construir y levantar los contenedores

```bash
docker compose build
docker compose up -d
```

La primera vez esto puede tardar **2-5 minutos** porque:
1. Descarga las imágenes de Docker (PHP, MariaDB, Nginx, Node.js)
2. Compila las extensiones PHP necesarias
3. Importa el dump SQL en MariaDB
4. Instala las dependencias de Composer
5. Genera la APP_KEY de Laravel
6. Crea el symlink de storage
7. Ejecuta las migraciones pendientes

### 5. Verificar que todo esté corriendo

```bash
docker compose ps
```

Deberías ver los 4 contenedores con estado `Up`:

```
NAME            IMAGE                    STATUS                  PORTS
success_app     successprojectbase-app   Up                      9000/tcp
success_mysql   mariadb:10.11            Up (healthy)            0.0.0.0:3307->3306/tcp
success_nginx   nginx:alpine             Up                      0.0.0.0:8001->80/tcp
success_node    node:20-alpine           Up                      0.0.0.0:5173->5173/tcp
```

### 6. Acceder a la aplicación

| Servicio | URL |
|---|---|
| **Aplicación web** | [http://localhost:8001](http://localhost:8001) |
| **Vite (HMR)** | [http://localhost:5173](http://localhost:5173) |
| **MariaDB** | `localhost:3307` (usuario: `success_user` / contraseña: `success_pass`) |

---

## 🏗️ Arquitectura del Entorno

```
┌──────────────────────────────────────────────────────┐
│                Docker Compose Network                │
│                                                      │
│  ┌────────────┐    FastCGI     ┌─────────────────┐   │
│  │   Nginx    │───────────────▶│   PHP-FPM 8.2   │   │
│  │  :8001     │    :9000       │   (Laravel App)  │   │
│  └────────────┘                │   + Cron         │   │
│                                │   + Queue Worker │   │
│  ┌────────────┐                └────────┬─────────┘   │
│  │  Node.js   │                         │ TCP         │
│  │  Vite HMR  │                         │ :3306       │
│  │  :5173     │                ┌────────▼─────────┐   │
│  └────────────┘                │  MariaDB 10.11   │   │
│                                │  :3307 (externo) │   │
│                                └──────────────────┘   │
└──────────────────────────────────────────────────────┘
```

| Contenedor | Función |
|---|---|
| **success_nginx** | Servidor web Nginx, proxy reverso hacia PHP-FPM |
| **success_app** | PHP-FPM 8.2 con la app Laravel, cron (scheduler) y queue worker via Supervisor |
| **success_mysql** | MariaDB 10.11 con persistencia en volumen Docker |
| **success_node** | Servidor Vite para compilación de assets con Hot Module Replacement |

---

## 📖 Comandos de Uso Diario

### Contenedores

```bash
# Levantar el entorno
docker compose up -d

# Detener el entorno
docker compose down

# Reiniciar todos los contenedores
docker compose restart

# Ver estado de los contenedores
docker compose ps

# Ver logs en tiempo real
docker compose logs -f

# Ver logs de un servicio específico
docker compose logs -f app
docker compose logs -f mysql
docker compose logs -f nginx
docker compose logs -f node
```

### Laravel (Artisan)

```bash
# Entrar al shell del contenedor PHP
docker compose exec app bash

# Ejecutar comandos artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:status
docker compose exec app php artisan db:seed
docker compose exec app php artisan tinker
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:list

# Migración fresh (⚠️ borra todos los datos)
docker compose exec app php artisan migrate:fresh --seed
```

### Base de Datos

```bash
# Acceder al CLI de MariaDB
docker compose exec mysql mariadb -u success_user -psuccess_pass success

# Exportar un backup de la base de datos
docker compose exec mysql mariadb-dump -u success_user -psuccess_pass success > backup.sql
```

### Frontend (Node.js / npm)

```bash
# Instalar dependencias npm
docker compose exec node npm install

# Compilar assets para producción
docker compose exec node npm run build
```

### Makefile (Atajos)

Si tienes `make` instalado, puedes usar estos atajos:

```bash
make up             # Levantar todo
make down           # Detener todo
make build          # Reconstruir imágenes
make restart        # Reiniciar contenedores
make logs           # Ver todos los logs
make shell          # Entrar al contenedor PHP
make mysql          # Abrir CLI de MariaDB
make migrate        # Ejecutar migraciones
make seed           # Ejecutar seeders
make fresh          # Fresh migration + seed
make tinker         # Abrir Laravel Tinker
make test           # Ejecutar tests
make cache-clear    # Limpiar caché de Laravel
make db-export      # Exportar backup de la BD
make destroy        # ⚠️ Eliminar TODO (contenedores + datos)
```

---

## 🔧 Troubleshooting

### El puerto 3306 está ocupado

Si tienes XAMPP u otro servicio MySQL corriendo, el puerto 3306 estará en uso. El entorno Docker usa el puerto **3307** externamente para evitar conflictos. Si aún hay problemas:

```bash
# Windows: Verificar qué usa el puerto
netstat -ano | findstr "3306"

# Detener XAMPP MySQL antes de usar Docker
```

### Permisos en storage/

```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### MariaDB no arranca o el dump no se importó

```bash
# Ver logs de MariaDB
docker compose logs mysql

# Si necesitas re-importar el dump, destruye volúmenes y recrea
docker compose down -v
docker compose up -d
```

### Vite no conecta (HMR no funciona)

```bash
# Verificar que el puerto 5173 no esté ocupado
netstat -ano | findstr "5173"

# Ver logs del contenedor Node
docker compose logs node
```

### Error "Table already exists" en migraciones

Esto es **normal** cuando se usa un dump SQL. El dump ya contiene las tablas, así que las migraciones no necesitan ejecutarse. El entrypoint maneja esto automáticamente.

### Reconstruir todo desde cero

```bash
# Opción nuclear: elimina contenedores, imágenes y volúmenes
docker compose down -v --rmi all
docker compose build --no-cache
docker compose up -d
```

---

## 📁 Estructura de Archivos Docker

```
successProjectBase/
├── Dockerfile                          # Imagen PHP-FPM 8.2 personalizada
├── docker-compose.yml                  # Orquestación de servicios
├── .env.docker                         # Variables de entorno para Docker
├── .dockerignore                       # Archivos excluidos del build
├── Makefile                            # Atajos de comandos
├── success.sql                         # Dump de la base de datos (no versionado)
└── docker/
    ├── nginx/
    │   └── default.conf                # Configuración de Nginx
    ├── php/
    │   ├── entrypoint.sh               # Script de inicio del contenedor
    │   └── local.ini                   # Configuración PHP para desarrollo
    ├── supervisor/
    │   └── supervisord.conf            # PHP-FPM + Cron + Queue Worker
    └── cron/
        └── laravel-cron                # Crontab para el scheduler de Laravel
```

---

## ⚙️ Stack Tecnológico

| Componente | Tecnología | Versión |
|---|---|---|
| Framework | Laravel | 12.x |
| PHP | PHP-FPM | 8.2 |
| Base de datos | MariaDB | 10.11 |
| Servidor web | Nginx | Alpine |
| Frontend | Vite + TailwindCSS 4 + Bootstrap 5 | - |
| Template | AdminLTE | 3.15 |
| Autenticación | Jetstream + Fortify + Sanctum | 5.x |
| Permisos | Spatie Laravel Permission | 6.x |
| PDF | DomPDF | 3.x |
| Excel | PhpSpreadsheet | 5.x |
| Broadcasting | Pusher | - |
| Process Manager | Supervisor | - |
