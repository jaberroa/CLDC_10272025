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
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    nginx \
    supervisor \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apk del build-dependencies

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
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Crear directorios necesarios
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Script de inicio optimizado
RUN echo '#!/bin/sh\n\
# Configurar puerto dinámico\n\
export PORT=${PORT:-8000}\n\
sed -i "s/\${PORT:-8000}/$PORT/g" /etc/nginx/sites-available/default\n\
\n\
# Iniciar servicios con supervisor\n\
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' > /start.sh && chmod +x /start.sh

# Exponer puerto
EXPOSE $PORT

# Comando de inicio
CMD ["/start.sh"]