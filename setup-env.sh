#!/bin/bash

echo "🔧 Configurando entorno CLDCI..."

# Crear archivo .env
cat > .env << 'EOF'
APP_NAME="CLDC"
APP_ENV=local
APP_KEY=base64:6NudpdNNuVZdvkv2BZm7oi3U/UNjsdLwNjnzwkfY5cI=
APP_DEBUG=true
APP_URL=http://localhost:8010

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cldciStaging
DB_USERNAME=cldciUser
DB_PASSWORD=2192Daa6251981*.*

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

echo "✅ Archivo .env creado"

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache

echo "✅ Directorios creados"

# Intentar cambiar permisos
echo "📁 Configurando permisos..."
chmod -R 775 storage/ 2>/dev/null || echo "⚠️  No se pudieron cambiar permisos de storage"
chmod -R 775 bootstrap/cache/ 2>/dev/null || echo "⚠️  No se pudieron cambiar permisos de bootstrap/cache"

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear 2>/dev/null || echo "⚠️  No se pudo limpiar config"
php artisan cache:clear 2>/dev/null || echo "⚠️  No se pudo limpiar cache"
php artisan view:clear 2>/dev/null || echo "⚠️  No se pudo limpiar views"

echo "✅ Configuración completada"
echo ""
echo "🌐 Para iniciar el servidor:"
echo "   php artisan serve --host=0.0.0.0 --port=8010"
echo ""
echo "📋 Si hay errores de permisos, ejecuta:"
echo "   sudo chown -R jaberroa:jaberroa storage bootstrap/cache"
