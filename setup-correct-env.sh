#!/bin/bash

echo "🔧 Configurando entorno con credenciales correctas de Docker..."

# Crear archivo .env con credenciales correctas
cat > .env << 'EOF'
APP_NAME="CLDC"
APP_ENV=local
APP_KEY=base64:6NudpdNNuVZdvkv2BZm7oi3U/UNjsdLwNjnzwkfY5cI=
APP_DEBUG=true
APP_URL=http://localhost:8010

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Base de datos MySQL en Docker (credenciales correctas)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cldc_database
DB_USERNAME=cldc_user
DB_PASSWORD=cldc_password

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

MAIL_MAILER=log
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

echo "✅ Archivo .env configurado con credenciales correctas"

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "✅ Configuración completada"
echo ""
echo "🌐 Para iniciar el servidor:"
echo "   php artisan serve --host=0.0.0.0 --port=8010"
echo ""
echo "📋 Base de datos: MySQL Docker (cldc_database)"

