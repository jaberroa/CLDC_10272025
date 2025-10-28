FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev libpq-dev postgresql-client nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Node.js 20.x (necesario para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Configurar extensiones PHP (para PostgreSQL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar configuración de Nginx y PHP-FPM
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copiar el código del proyecto
COPY . .

# Instalar dependencias PHP y JS
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build && npm prune --omit=dev

# Crear directorios y permisos
RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cachear configuración y vistas (ignora errores si .env no está disponible en build)
RUN php artisan config:clear || true
RUN php artisan view:clear || true

# Exponer el puerto que Render asigna dinámicamente
EXPOSE $PORT

# Script de inicio
RUN echo '#!/bin/bash\n\
export PORT=${PORT:-80}\n\
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/sites-available/default\n\
php-fpm -D\n\
nginx\n\
echo "Esperando conexión con la base de datos..."\n\
for i in {1..30}; do\n\
  php artisan migrate:status > /dev/null 2>&1 && break\n\
  echo "DB no lista, reintentando ($i/30)..." && sleep 2\n\
done\n\
php artisan migrate --force --no-interaction || true\n\
tail -f /var/log/nginx/access.log' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
