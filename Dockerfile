# ---- Etapa base ----
FROM php:8.3-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-install pdo_pgsql gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir directorio de trabajo
WORKDIR /var/www/html

# Copiar el código del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Crear carpetas necesarias
RUN mkdir -p storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Cachear configuración de Laravel
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Exponer el puerto que Railway asigna (por defecto 8080)
EXPOSE 8080

# Comando para iniciar Laravel usando el servidor embebido de PHP
CMD php -S 0.0.0.0:${PORT:-8080} -t public
