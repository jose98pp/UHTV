# Ultima Hora TV (UHTV) - Plataforma de Noticias Digital

Plataforma web de noticias y streaming digital para **Ultima Hora TV**, desarrollada con **Laravel 10**, **Tailwind CSS**, **Blade**, **Vite** y **MySQL**.

---

## 🚀 Inicio Rápido con Docker

El proyecto está 100% dockerizado e incluye la base de datos completa con datos reales para que cualquier programador pueda comenzar a trabajar de inmediato sin configuraciones complejas.

### 1. Clonar y configurar
```bash
git clone https://github.com/jose98pp/UHTV.git
cd UHTV
cp .env.docker .env   # En Windows: copy .env.docker .env
```

### 2. Levantar el entorno
```bash
docker compose up -d --build
docker compose exec app npm install
docker compose exec app npm run build
```

### 3. Acceder
- **Web Pública:** [http://localhost:8000](http://localhost:8000)
- **Panel Administrativo:** [http://localhost:8000/login](http://localhost:8000/login)
  - **Email:** `admin@uhtv.com`
  - **Contraseña:** `admin123`

Para ver la guía completa con detalles de arquitectura, base de datos, comandos útiles y solución de problemas, consulta [DOCKER.md](DOCKER.md).

---

## 🧪 Pruebas Automatizadas (Tests)

Para ejecutar la suite de pruebas del proyecto:

```bash
# Con Docker:
docker compose exec app php artisan test

# En entorno local (Laragon/PHP local):
php artisan test
```

---

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.2 / Laravel 10.x
- **Frontend:** Blade, Tailwind CSS, JavaScript (ES6+), Vite
- **Base de Datos:** MySQL 8.0
- **Servidor Web:** Nginx 1.27
- **Contenedores:** Docker & Docker Compose
