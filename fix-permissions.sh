#!/bin/bash

echo "🔧 Solucionando permisos de Laravel..."

# Crear directorios si no existen
mkdir -p storage/logs
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache

# Intentar cambiar permisos
echo "📁 Configurando permisos de storage..."
chmod -R 775 storage/ 2>/dev/null || echo "⚠️  No se pudieron cambiar permisos de storage"
chmod -R 775 bootstrap/cache/ 2>/dev/null || echo "⚠️  No se pudieron cambiar permisos de bootstrap/cache"

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear 2>/dev/null || echo "⚠️  No se pudo limpiar config"
php artisan cache:clear 2>/dev/null || echo "⚠️  No se pudo limpiar cache"
php artisan view:clear 2>/dev/null || echo "⚠️  No se pudo limpiar views"

echo "✅ Proceso completado"
echo ""
echo "🌐 Accede a: http://localhost:8001"
echo "📋 Si hay errores, revisa los logs en storage/logs/"
