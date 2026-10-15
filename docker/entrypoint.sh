#!/bin/sh
set -e

echo "🚀 Iniciando contenedor de Ultima Hora TV..."

# Copiar .env si no existe
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.docker ]; then
        echo "📄 Copiando .env.docker a .env..."
        cp /var/www/html/.env.docker /var/www/html/.env
    elif [ -f /var/www/html/.env.example ]; then
        echo "📄 Copiando .env.example a .env..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# Instalar dependencias de Composer si vendor no existe
if [ ! -d /var/www/html/vendor ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar APP_KEY si no está configurada
if ! grep -q "^APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Asegurar directorios de almacenamiento y caché
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

# Ajustar permisos para www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear symlink de almacenamiento si no existe
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creando enlace simbólico storage:link..."
    php artisan storage:link || true
fi

echo "✅ Entorno listo. Iniciando PHP-FPM..."
exec php-fpm
