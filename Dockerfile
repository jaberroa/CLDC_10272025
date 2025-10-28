# -----------------------
# ✅ ETAPA 1 — Build con Node
# -----------------------
    FROM node:20-alpine AS node-builder

    WORKDIR /app
    COPY package*.json ./
    RUN npm ci && npm cache clean --force
    
    COPY . .
    RUN npm run build
    
    # -----------------------
    # ✅ ETAPA 2 — PHP + NGINX + Supervisor
    # -----------------------
    FROM php:8.3-fpm-alpine
    
    # Instalar dependencias necesarias
    RUN apk add --no-cache \
        nginx \
        supervisor \
        git \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        postgresql-dev \
        oniguruma-dev
    
    # PHP Extensions
    RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip
    
    # ✅ Asegurar que PHP-FPM escuche en TCP para Render
    RUN sed -i 's|listen = /var/run/php/php8.3-fpm.sock|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf
    
    # Copiar composer
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
    
    WORKDIR /var/www/html
    
    # Copiar proyecto
    COPY . .
    
    # ✅ Copiar build de Vite
    COPY --from=node-builder /app/public/build ./public/build
    
    # ✅ Instalar dependencias PHP de producción
    RUN composer install --no-dev --optimize-autoloader --no-interaction
    
    # Permisos Laravel
    RUN chown -R www-data:www-data /var/www/html \
        && chmod -R 755 storage bootstrap/cache
    
    # -----------------------
    # ✅ Configuración de servicios
    # -----------------------
    COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
    COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
    COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
    
    # Script de inicio
    RUN echo '#!/bin/sh' > /start.sh && \
        echo 'php artisan config:cache' >> /start.sh && \
        echo 'php artisan route:cache' >> /start.sh && \
        echo 'php artisan view:cache' >> /start.sh && \
        echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /start.sh && \
        chmod +x /start.sh
    
    # Puerto obligatorio en Render
    ENV PORT=10000
    EXPOSE 10000
    
    CMD ["/start.sh"]
    