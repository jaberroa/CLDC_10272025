#!/bin/bash

echo "🚀 Iniciando entorno de desarrollo CLDCI..."

# Verificar que Docker esté ejecutándose
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está ejecutándose. Por favor, inicia Docker primero."
    exit 1
fi

# Iniciar contenedores
echo "📦 Iniciando contenedores..."
docker-compose up -d

# Esperar a que los contenedores estén listos
echo "⏳ Esperando a que los contenedores estén listos..."
sleep 10

# Corregir permisos
echo "🔧 Configurando permisos..."
docker-compose exec app chown -R www-data:www-data storage/ bootstrap/cache/
docker-compose exec app chmod -R 775 storage/ bootstrap/cache/

# Limpiar cachés
echo "🧹 Limpiando cachés..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Ejecutar migraciones si es necesario
echo "🗄️ Verificando migraciones..."
docker-compose exec app php artisan migrate --force

# Verificar que todo funciona
echo "✅ Verificando que Laravel puede escribir en logs..."
docker-compose exec app php artisan tinker --execute="\Log::info('Entorno iniciado correctamente - ' . now()); echo 'Log escrito correctamente';"

echo ""
echo "🎉 ¡Entorno de desarrollo listo!"
echo "🌐 Aplicación: http://localhost:8000"
echo "🗄️ PHPMyAdmin: http://localhost:8080"
echo ""
echo "📝 Para detener el entorno: docker-compose down"
echo "🔧 Para corregir permisos: ./fix-permissions.sh"


