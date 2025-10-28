FROM php:8.3-fpm

# Install system dependencies including Node.js and Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libgd-dev \
    postgresql-client \
    libpq-dev \
    nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x (required for Vite 7.x)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Configure and install PHP extensions (PostgreSQL instead of MySQL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy PHP-FPM configuration
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy application code
COPY . .

# Create .env file with PostgreSQL configuration for Render
RUN echo "APP_NAME=\"CLDC\"" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://cldc.onrender.com" >> .env && \
    echo "" >> .env && \
    echo "LOG_CHANNEL=stack" >> .env && \
    echo "LOG_DEPRECATIONS_CHANNEL=null" >> .env && \
    echo "LOG_LEVEL=error" >> .env && \
    echo "" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=\${DB_HOST}" >> .env && \
    echo "DB_PORT=\${DB_PORT}" >> .env && \
    echo "DB_DATABASE=\${DB_DATABASE}" >> .env && \
    echo "DB_USERNAME=\${DB_USERNAME}" >> .env && \
    echo "DB_PASSWORD=\${DB_PASSWORD}" >> .env && \
    echo "" >> .env && \
    echo "BROADCAST_DRIVER=log" >> .env && \
    echo "CACHE_DRIVER=file" >> .env && \
    echo "FILESYSTEM_DISK=local" >> .env && \
    echo "QUEUE_CONNECTION=sync" >> .env && \
    echo "SESSION_DRIVER=file" >> .env && \
    echo "SESSION_LIFETIME=120" >> .env && \
    echo "" >> .env && \
    echo "MEMCACHED_HOST=127.0.0.1" >> .env && \
    echo "" >> .env && \
    echo "REDIS_HOST=127.0.0.1" >> .env && \
    echo "REDIS_PASSWORD=null" >> .env && \
    echo "REDIS_PORT=6379" >> .env && \
    echo "" >> .env && \
    echo "MAIL_MAILER=smtp" >> .env && \
    echo "MAIL_HOST=mailpit" >> .env && \
    echo "MAIL_PORT=1025" >> .env && \
    echo "MAIL_USERNAME=null" >> .env && \
    echo "MAIL_PASSWORD=null" >> .env && \
    echo "MAIL_ENCRYPTION=null" >> .env && \
    echo "MAIL_FROM_ADDRESS=\"hello@example.com\"" >> .env && \
    echo "MAIL_FROM_NAME=\"\${APP_NAME}\"" >> .env && \
    echo "" >> .env && \
    echo "AWS_ACCESS_KEY_ID=" >> .env && \
    echo "AWS_SECRET_ACCESS_KEY=" >> .env && \
    echo "AWS_DEFAULT_REGION=us-east-1" >> .env && \
    echo "AWS_BUCKET=" >> .env && \
    echo "AWS_USE_PATH_STYLE_ENDPOINT=false" >> .env && \
    echo "" >> .env && \
    echo "PUSHER_APP_ID=" >> .env && \
    echo "PUSHER_APP_KEY=" >> .env && \
    echo "PUSHER_APP_SECRET=" >> .env && \
    echo "PUSHER_HOST=" >> .env && \
    echo "PUSHER_PORT=443" >> .env && \
    echo "PUSHER_SCHEME=https" >> .env && \
    echo "PUSHER_APP_CLUSTER=mt1" >> .env && \
    echo "" >> .env && \
    echo "VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"" >> .env && \
    echo "VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"" >> .env && \
    echo "VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"" >> .env && \
    echo "VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"" >> .env && \
    echo "VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"" >> .env && \
    echo "CACHE_STORE=file" >> .env && \
    echo "FORCE_HTTPS=true" >> .env && \
    echo "ASSET_URL=https://cldc.onrender.com" >> .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies (including dev dependencies for build)
RUN npm ci
RUN npm run build

# Remove dev dependencies after build to reduce image size
RUN npm prune --omit=dev

# Verify assets were built correctly
RUN ls -la public/build/assets/ || echo "Warning: Assets directory not found"

# Create necessary directories and set permissions
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generate application key if not exists
RUN php artisan key:generate --no-interaction || true

# Cache configuration for production
RUN php artisan config:cache || true
RUN php artisan view:cache || true

# Don't cache routes in production to avoid 404 issues
RUN php artisan route:clear || true

# Expose port (Render uses $PORT environment variable)
EXPOSE $PORT

# Create startup script
RUN echo '#!/bin/bash\n\
# Set PORT environment variable\n\
export PORT=${PORT:-80}\n\
# Replace PORT placeholder in nginx config\n\
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/sites-available/default\n\
# Run migrations\n\
php artisan migrate --force --no-interaction\n\
# Start PHP-FPM in background\n\
php-fpm -D\n\
# Start Nginx in foreground\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

# Start Nginx + PHP-FPM
CMD /start.sh
