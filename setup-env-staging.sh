#!/bin/bash

# Script para configurar variables de entorno en Elastic Beanstalk
# Uso: ./setup-env-staging.sh

echo "🚀 Configurando variables de entorno en Elastic Beanstalk..."

# Verificar que el archivo .env.staging existe
if [ ! -f ".env.staging" ]; then
    echo "❌ Error: No se encontró .env.staging"
    echo "Por favor, crea el archivo .env.staging con las variables necesarias"
    exit 1
fi

# Verificar que eb CLI está instalado
if ! command -v eb &> /dev/null; then
    echo "❌ Error: EB CLI no está instalado"
    echo "Instala con: pip install awsebcli"
    exit 1
fi

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "❌ Error: No se encontró composer.json"
    echo "Ejecuta este script desde la raíz del proyecto"
    exit 1
fi

echo "📋 Configurando variables de entorno para cldci-staging-env..."

# Configurar variables de entorno desde .env.staging
# Filtrar comentarios y líneas vacías, luego convertir a formato eb setenv
grep -v '^#' .env.staging | grep -v '^$' | while IFS='=' read -r key value; do
    if [ -n "$key" ] && [ -n "$value" ]; then
        echo "🔧 Configurando $key..."
        eb setenv "$key=$value" --environment cldci-staging-env
    fi
done

echo ""
echo "✅ Variables de entorno configuradas correctamente"
echo "🌐 Aplicación disponible en: http://cldci-staging-env.eba-xphp7eqe.us-east-1.elasticbeanstalk.com"
echo ""
echo "📊 Para verificar las variables configuradas:"
echo "eb printenv --environment cldci-staging-env"
