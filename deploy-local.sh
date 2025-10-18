#!/bin/bash

# Script para probar el deployment localmente
# Uso: ./deploy-local.sh

echo "🚀 Iniciando deployment local..."

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "❌ Error: No se encontró composer.json. Ejecuta este script desde la raíz del proyecto."
    exit 1
fi

# Crear directorio de despliegue
echo "📦 Creando paquete de despliegue..."
mkdir -p deploy-package

# Copiar archivos necesarios para Laravel
cp -r app bootstrap config database public resources routes storage vendor composer.json composer.lock artisan deploy-package/

# Crear archivo .env para staging (simulado)
cat > deploy-package/.env << 'EOF'
APP_NAME="CLDCI - Staging"
APP_ENV=staging
APP_KEY=base64:iJKg0rdmQQB7NiVSVrUN1psj3t5eRmz/AFzBexYU
APP_DEBUG=false
APP_URL=https://staging.cldc.org.do

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=cldci-staging.c4rie2uost3w.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=cldci_staging
DB_USERNAME=cldci_user
DB_PASSWORD=2192Daa6251981*.*

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@cldc.org.do
MAIL_FROM_NAME=CLDCI
EOF

# Copiar archivos de configuración de Elastic Beanstalk
cp -r .ebextensions deploy-package/
cp -r .elasticbeanstalk deploy-package/
cp Dockerrun.aws.json deploy-package/

# Crear Dockerfile para el paquete
cp Dockerfile.eb deploy-package/Dockerfile

echo "✅ Paquete de despliegue creado en deploy-package/"
echo "📋 Contenido del paquete:"
ls -la deploy-package/

echo ""
echo "🔧 Para probar localmente:"
echo "cd deploy-package && docker build -t cldc-test ."
echo "docker run -p 8080:80 cldc-test"
echo ""
echo "🌐 Luego accede a: http://localhost:8080"
