#!/bin/bash
# Script para arreglar permisos de storage

echo "🔧 Arreglando permisos de storage..."

# Arreglar permisos de storage
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage

# Limpiar cache
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear

echo "✅ Permisos de storage arreglados"