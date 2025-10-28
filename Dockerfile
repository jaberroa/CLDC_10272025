# Multi-stage build para optimizar tamaño de imagen
FROM node:20-alpine AS node-builder

# Instalar dependencias Node.js
WORKDIR /app
COPY package*.json ./
RUN npm ci && npm cache clean --force

# Build de assets
COPY . .
RUN npm run build

# Imagen final optimizada
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema de forma optimizada
RUN apk add --no-cache --virtual .build-deps \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apk del .build-deps

# Instalar nginx y supervisor por separado
RUN apk add --no-cache nginx supervisor

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de configuración
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar código de la aplicación
COPY . .

# Copiar assets buildados desde node-builder
COPY --from=node-builder /app/public/build ./public/build

# Instalar dependencias PHP (solo producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-req=ext-pdo_pgsql --ignore-platform-req=ext-zip --ignore-platform-req=ext-gd

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Crear directorios necesarios
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache /var/log/supervisor

# Crear archivo .env con configuración básica
RUN echo 'APP_NAME="CLDC"' > .env && \
    echo 'APP_ENV=production' >> .env && \
    echo 'APP_KEY=' >> .env && \
    echo 'APP_DEBUG=false' >> .env && \
    echo 'APP_URL=https://cldc.onrender.com' >> .env && \
    echo 'LOG_CHANNEL=stack' >> .env && \
    echo 'LOG_LEVEL=error' >> .env && \
    echo 'DB_CONNECTION=pgsql' >> .env && \
    echo 'DB_HOST=' >> .env && \
    echo 'DB_PORT=5432' >> .env && \
    echo 'DB_DATABASE=' >> .env && \
    echo 'DB_USERNAME=' >> .env && \
    echo 'DB_PASSWORD=' >> .env && \
    echo 'CACHE_DRIVER=database' >> .env && \
    echo 'SESSION_DRIVER=database' >> .env && \
    echo 'QUEUE_CONNECTION=sync' >> .env && \
    echo 'MAIL_MAILER=log' >> .env && \
    echo 'BROADCAST_DRIVER=log' >> .env && \
    echo 'FILESYSTEM_DISK=local' >> .env

# Script de inicio optimizado
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'export PORT=${PORT:-8000}' >> /start.sh && \
    echo 'sed -i "s/\${PORT:-8000}/$PORT/g" /etc/nginx/sites-available/default' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Actualizar .env con variables de entorno de Render' >> /start.sh && \
    echo 'if [ ! -z "$DB_HOST" ]; then sed -i "s/DB_HOST=/DB_HOST=$DB_HOST/" .env; fi' >> /start.sh && \
    echo 'if [ ! -z "$DB_DATABASE" ]; then sed -i "s/DB_DATABASE=/DB_DATABASE=$DB_DATABASE/" .env; fi' >> /start.sh && \
    echo 'if [ ! -z "$DB_USERNAME" ]; then sed -i "s/DB_USERNAME=/DB_USERNAME=$DB_USERNAME/" .env; fi' >> /start.sh && \
    echo 'if [ ! -z "$DB_PASSWORD" ]; then sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env; fi' >> /start.sh && \
    echo 'if [ ! -z "$APP_KEY" ]; then sed -i "s/APP_KEY=/APP_KEY=$APP_KEY/" .env; fi' >> /start.sh && \
    echo '' >> /start.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

# Exponer puerto
EXPOSE $PORT

# Comando de inicio
CMD ["/start.sh"]