#!/bin/bash

echo "🚀 Configurando CLDCI - Sistema de Gestión"
echo "=========================================="

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "❌ Error: No se encontró composer.json. Ejecuta este script desde la raíz del proyecto."
    exit 1
fi

# Crear archivo .env si no existe
if [ ! -f ".env" ]; then
    echo "📝 Creando archivo .env..."
    cp .env.example .env
    echo "✅ Archivo .env creado"
else
    echo "ℹ️  Archivo .env ya existe"
fi

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
php artisan key:generate

# Configurar permisos de storage
echo "📁 Configurando permisos de storage..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Ejecutar migraciones
echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "✅ Configuración completada exitosamente!"
echo ""
echo "📋 Próximos pasos:"
echo "1. Inicia el servidor: php artisan serve"
echo "2. O usa Docker: docker-compose up -d"
echo "3. Accede a: http://localhost:8000"
echo ""
echo "🔐 Credenciales de acceso:"
echo "   - Email: admin@cldci.org.do"
echo "   - Contraseña: password"
echo ""
echo "📊 Datos de demostración incluidos:"
echo "   - 5 miembros de ejemplo"
echo "   - 32 seccionales provinciales"
echo "   - 8 seccionales internacionales"
echo "   - Cursos y asambleas programadas"
echo ""
echo "🎉 ¡Sistema CLDCI listo para usar!"
