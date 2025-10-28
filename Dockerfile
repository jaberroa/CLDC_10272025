FROM php:8.3-fpm

# Instalar dependencias del sistema incluyendo Node.js y Nginx
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

# Instalar Node.js 20.x (requerido para Vite 7.x)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Configurar e instalar extensiones PHP (PostgreSQL en lugar de MySQL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Obtener Composer más reciente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar configuración PHP-FPM
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copiar configuración Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copiar código de la aplicación
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias Node.js (incluyendo dev dependencies para build)
RUN npm ci
RUN npm run build

# Eliminar dev dependencies después del build para reducir tamaño de imagen
RUN npm prune --omit=dev

# Verificar que los assets se construyeron correctamente
RUN ls -la public/build/assets/ || echo "Warning: Assets directory not found"

# Crear directorios necesarios y establecer permisos
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Generar clave de aplicación si no existe
RUN php artisan key:generate --no-interaction || true

# Cachear configuración para producción
RUN php artisan config:cache || true
RUN php artisan view:cache || true

# No cachear rutas en producción para evitar errores 404
RUN php artisan route:clear || true

# Exponer puerto (Render usa variable de entorno $PORT)
EXPOSE $PORT

# Crear script de inicio
RUN echo '#!/bin/bash\n\
# Establecer variable de entorno PORT\n\
export PORT=${PORT:-80}\n\
# Reemplazar placeholder PORT en configuración nginx\n\
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/sites-available/default\n\
# Iniciar PHP-FPM en background primero\n\
php-fpm -D\n\
# Iniciar Nginx en background\n\
nginx\n\
# Debug: Mostrar variables de entorno de base de datos\n\
echo "🔍 Variables de entorno de DB:"\n\
echo "DB_HOST: $DB_HOST"\n\
echo "DB_PORT: $DB_PORT"\n\
echo "DB_DATABASE: $DB_DATABASE"\n\
echo "DB_USERNAME: $DB_USERNAME"\n\
echo "DB_PASSWORD: [HIDDEN]"\n\
# Esperar un poco para que los servicios se estabilicen\n\
echo "⏳ Esperando 10 segundos para estabilización..."\n\
sleep 10\n\
# Intentar conectar con psql y mostrar errores detallados\n\
echo "🔌 Probando conexión con psql..."\n\
PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -p $DB_PORT -U $DB_USERNAME -d $DB_DATABASE -c "\\q" 2>&1\n\
if [ $? -eq 0 ]; then\n\
  echo "✅ Conexión a base de datos exitosa"\n\
  echo "🚀 Ejecutando migraciones..."\n\
  php artisan migrate --force --no-interaction -v\n\
  if [ $? -eq 0 ]; then\n\
    echo "✅ Migraciones completadas exitosamente"\n\
    echo "👤 Creando usuario administrador..."\n\
    php artisan tinker --execute="\App\Models\User::create([\"name\" => \"Administrador\", \"email\" => \"admin@cldci.org\", \"password\" => bcrypt(\"admin123\"), \"email_verified_at\" => now()]);"\n\
    echo "✅ Usuario administrador creado: admin@cldci.org / admin123"\n\
    echo "🔧 Habilitando debug de Laravel..."\n\
    php artisan config:clear\n\
    php artisan cache:clear\n\
    php artisan view:clear\n\
    echo "🔍 Verificando configuración de Laravel..."\n\
    php artisan config:show app.debug\n\
    php artisan config:show app.key\n\
  else\n\
    echo "❌ Migraciones fallaron, pero continuando..."\n\
  fi\n\
else\n\
  echo "⚠️ No se pudo conectar a la base de datos, pero intentando migraciones de todas formas..."\n\
  php artisan migrate --force --no-interaction -v\n\
  if [ $? -eq 0 ]; then\n\
    echo "✅ Migraciones completadas exitosamente (sin verificación previa)"\n\
    echo "👤 Creando usuario administrador..."\n\
    php artisan tinker --execute="\App\Models\User::create([\"name\" => \"Administrador\", \"email\" => \"admin@cldci.org\", \"password\" => bcrypt(\"admin123\"), \"email_verified_at\" => now()]);"\n\
    echo "✅ Usuario administrador creado: admin@cldci.org / admin123"\n\
    echo "🔧 Habilitando debug de Laravel..."\n\
    php artisan config:clear\n\
    php artisan cache:clear\n\
    php artisan view:clear\n\
    echo "🔍 Verificando configuración de Laravel..."\n\
    php artisan config:show app.debug\n\
    php artisan config:show app.key\n\
  else\n\
    echo "❌ Migraciones fallaron completamente"\n\
  fi\n\
fi\n\
# Mantener el contenedor ejecutándose\n\
tail -f /var/log/nginx/access.log' > /start.sh && chmod +x /start.sh

# Iniciar Nginx + PHP-FPM
CMD ["/start.sh"]
