#!/bin/bash

echo "🚀 Optimizando aplicación para producción..."

# Limpiar caches
echo "🧹 Limpiando caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoloader
echo "📦 Optimizando autoloader..."
composer install --no-dev --optimize-autoloader --no-interaction

# Verificar permisos
echo "🔐 Configurando permisos..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

echo "✅ Optimización completada!"
