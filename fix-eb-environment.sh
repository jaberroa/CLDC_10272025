#!/bin/bash

echo "🔧 Solucionando problema de Elastic Beanstalk..."
echo "El entorno está esperando versión 'Sample' pero recibe Laravel"
echo ""

# Verificar que AWS CLI está disponible
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI no está instalado"
    echo "Instala con: sudo snap install aws-cli"
    exit 1
fi

echo "📋 Opciones para solucionar el problema:"
echo "========================================"
echo ""
echo "OPCIÓN 1: Eliminar y recrear el entorno (RECOMENDADO)"
echo "-----------------------------------------------------"
echo "1. Eliminar el entorno actual: cldci-staging-env"
echo "2. Crear un nuevo entorno con la configuración correcta"
echo "3. Configurar las variables de entorno"
echo ""
echo "OPCIÓN 2: Forzar deployment de nueva versión"
echo "--------------------------------------------"
echo "1. Crear una nueva versión de aplicación"
echo "2. Forzar el deployment ignorando la versión 'Sample'"
echo ""

echo "🚀 Ejecutando OPCIÓN 2 (más rápida)..."
echo ""

# Crear nueva versión de aplicación
echo "📦 Creando nueva versión de aplicación..."

# Obtener la última versión exitosa
LATEST_VERSION=$(aws elasticbeanstalk describe-application-versions \
    --application-name cldci-staging \
    --region us-east-1 \
    --query 'ApplicationVersions[?Status==`Processed`] | sort_by(@, &DateCreated) | [-1].VersionLabel' \
    --output text)

echo "Última versión procesada: $LATEST_VERSION"

# Crear nueva versión con timestamp
NEW_VERSION="laravel-$(date +%Y%m%d-%H%M%S)"
echo "Nueva versión: $NEW_VERSION"

echo ""
echo "📋 Pasos manuales necesarios en AWS Console:"
echo "============================================"
echo ""
echo "1. Ve a AWS Console > Elastic Beanstalk > cldci-staging-env"
echo "2. Ve a 'Versiones de la aplicación' (Application versions)"
echo "3. Busca la versión más reciente (870e059db426b4a9a46456dc9fa113f8b3d2ace8-4)"
echo "4. Haz clic en 'Deploy' en esa versión"
echo "5. Selecciona el entorno: cldci-staging-env"
echo "6. Haz clic en 'Deploy'"
echo ""
echo "ALTERNATIVA: Eliminar y recrear el entorno"
echo "==========================================="
echo "1. Elimina el entorno actual: cldci-staging-env"
echo "2. Crea un nuevo entorno con:"
echo "   - Plataforma: Docker running on 64bit Amazon Linux 2023"
echo "   - Código fuente: Upload your code"
echo "   - Variables de entorno: Configurar desde .env.staging"
echo ""

echo "🎯 Recomendación: Usar la OPCIÓN 1 (recrear entorno) para evitar problemas futuros"
