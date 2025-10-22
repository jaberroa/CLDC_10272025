#!/bin/bash

# Script para corregir permisos de Laravel en Docker
echo "🔧 Corrigiendo permisos de Laravel..."

# Cambiar propietario a www-data
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chown -R www-data:www-data bootstrap/cache/

# Dar permisos de escritura
docker-compose exec app chmod -R 775 storage/
docker-compose exec app chmod -R 775 bootstrap/cache/

# Limpiar cachés
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

echo "✅ Permisos corregidos exitosamente"
echo "📝 Verificando que Laravel puede escribir en logs..."

# Probar escritura en logs
docker-compose exec app php artisan tinker --execute="\Log::info('Permisos corregidos - ' . now()); echo 'Log escrito correctamente';"

echo "🎉 ¡Listo! Los permisos están corregidos."