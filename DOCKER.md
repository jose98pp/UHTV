# 🐳 Guía de Desarrollo con Docker - Ultima Hora TV (UHTV)

Esta guía permite a cualquier programador clonar este repositorio y tener el entorno completo funcionando en minutos, con la misma versión de PHP, extensiones, servidor Nginx y la base de datos MySQL con todos sus datos y estructura actualizados.

---

## 🏛️ Arquitectura del Entorno

El proyecto se compone de 3 servicios orquestados mediante **Docker Compose**:

| Servicio | Contenedor | Descripción | Puerto Host | Puerto Interno |
| :--- | :--- | :--- | :--- | :--- |
| **`app`** | `uhtv_app` | PHP 8.2-FPM + Composer 2 + Node.js 20 + Extensiones (`gd` con WebP/Freetype, `pdo_mysql`, `bcmath`, `exif`, etc.) | - | `9000` |
| **`web`** | `uhtv_web` | Servidor Nginx 1.27 optimizado con gzip y FastCGI para Laravel | `8000` | `80` |
| **`db`** | `uhtv_db` | MySQL 8.0 con carga inicial automática del volcado completo | `33066` | `3306` |

---

## 📋 Requisitos Previos

- Tener instalado [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows / macOS / Linux) con Docker Compose habilitado.
- Git instalado.

---

## 🚀 Puesta en Marcha Rápida (Quickstart)

### 1. Clonar el repositorio
```bash
git clone https://github.com/jose98pp/UHTV.git
cd UHTV
```

### 2. Configurar el archivo de entorno (.env)
Se incluye una plantilla preconfigurada para Docker:
```bash
# En Windows (PowerShell / CMD):
copy .env.docker .env

# En Linux / macOS / Git Bash:
cp .env.docker .env
```

> **Nota:** Si olvidas este paso, el contenedor `app` copiará automáticamente `.env.docker` a `.env` en su primer arranque.

### 3. Levantar los contenedores
```bash
docker compose up -d --build
```
> En la primera ejecución:
> - Descargará las imágenes base y compilará las extensiones PHP necesarias.
> - El contenedor MySQL (`uhtv_db`) detectará el script `docker/database/init.sql` y cargará automáticamente la base de datos `bd_noti` con todas sus tablas, noticias, tasas BCB, usuarios y configuraciones.
> - El contenedor `uhtv_app` instalará las dependencias de Composer, generará el `APP_KEY` y creará el enlace simbólico `storage:link`.

### 4. Compilar los recursos del Frontend (Vite)
Para compilar los estilos y scripts Tailwind/Vite dentro del contenedor:
```bash
docker compose exec app npm install
docker compose exec app npm run build
```

### 5. ¡Listo para desarrollar!
Abre tu navegador en:
- 🌐 **Sitio Web Principal:** [http://localhost:8000](http://localhost:8000)
- 🔐 **Panel de Administración (Login):** [http://localhost:8000/login](http://localhost:8000/login)

---

## 👤 Credenciales de Acceso

La base de datos importada ya contiene los siguientes usuarios activos:

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@uhtv.com` | `admin123` |
| **Usuario Test** | `test@uhtv.com` | `test123` |

Si deseas recrear o actualizar los usuarios por defecto, puedes ejecutar:
```bash
docker compose exec app php artisan db:seed --class=AdminUserSeeder
```

---

## 🗄️ Base de Datos (`bd_noti`)

### Conexión desde el Host (DBeaver, HeidiSQL, TablePlus, MySQL Workbench)
Si deseas conectarte directamente a la base de datos desde tu gestor de base de datos favorito en tu máquina anfitriona:

- **Host:** `localhost` o `127.0.0.1`
- **Puerto:** `33066` *(puerto expuesto para evitar conflictos con MySQL locales en 3306)*
- **Base de datos:** `bd_noti`
- **Usuario:** `uhtv_user`
- **Contraseña:** `uhtv_secret`
- *(Usuario Root alternativo):* `root` / `root_secret`

### Cómo funciona el volcado inicial (`init.sql`)
- El archivo `docker/database/init.sql` contiene el esquema completo y datos del proyecto.
- MySQL solo ejecuta este archivo la **primera vez** que se inicializa el volumen `uhtv_db_data`.
- Si en algún momento deseas **reiniciar la base de datos desde cero al estado inicial del repositorio**:
  ```bash
  docker compose down -v
  docker compose up -d
  ```
  *(El parámetro `-v` elimina el volumen persistente anterior y fuerza a MySQL a volver a importar `init.sql` limpio).*

---

## 🧪 Ejecutar Pruebas Automatizadas (Tests)

Para ejecutar la suite de pruebas de Laravel dentro del contenedor:

```bash
# Ejecutar todos los tests
docker compose exec app php artisan test

# Ejecutar una prueba específica
docker compose exec app php artisan test tests/Feature/PwaTest.php
docker compose exec app php artisan test tests/Feature/HeaderBcbExchangeTest.php
```

---

## 🛠️ Comandos Frecuentes de Desarrollo

### Acceder a la terminal del contenedor PHP
```bash
docker compose exec app bash
```

### Limpiar cachés de Laravel
```bash
docker compose exec app php artisan optimize:clear
```

### Ver logs en tiempo real
```bash
# Ver todos los logs
docker compose logs -f

# Ver logs de un servicio específico
docker compose logs -f app
docker compose logs -f web
docker compose logs -f db
```

### Detener los contenedores
```bash
# Pausar los servicios sin perder datos
docker compose stop

# Detener y remover los contenedores (conservando la base de datos)
docker compose down

# Reiniciar todos los servicios
docker compose restart
```

### Desarrollo Frontend en vivo con Vite
Si vas a realizar modificaciones continuas de CSS/JS en el frontend:
```bash
docker compose exec app npm run dev
```

---

## 🔧 Solución de Problemas Comunes

### 1. El puerto 8000 ya está en uso en tu máquina
En tu archivo `.env`, puedes definir un puerto alternativo:
```env
APP_PORT=8080
```
Luego reinicia con `docker compose up -d`. El sitio estará en `http://localhost:8080`.

### 2. El puerto 33066 está en uso
En tu archivo `.env`, define otro puerto para MySQL:
```env
FORWARD_DB_PORT=3307
```

### 3. Las imágenes de noticias o banners no se ven
Asegúrate de que el enlace simbólico del storage esté creado:
```bash
docker compose exec app php artisan storage:link
```
Y si es necesario, verifica los permisos:
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```
