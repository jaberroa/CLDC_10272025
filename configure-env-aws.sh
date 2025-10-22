#!/bin/bash

# Script para configurar variables de entorno usando AWS CLI
# Uso: ./configure-env-aws.sh

echo "🚀 Configurando variables de entorno en Elastic Beanstalk usando AWS CLI..."

# Verificar que AWS CLI está instalado
if ! command -v aws &> /dev/null; then
    echo "❌ Error: AWS CLI no está instalado"
    echo "Instala con: sudo snap install aws-cli"
    exit 1
fi

# Verificar que el archivo .env.staging existe
if [ ! -f ".env.staging" ]; then
    echo "❌ Error: No se encontró .env.staging"
    echo "Por favor, crea el archivo .env.staging con las variables necesarias"
    exit 1
fi

echo "📋 Configurando variables de entorno para cldci-staging-env..."

# Configurar variables de entorno desde .env.staging usando AWS CLI
while IFS='=' read -r key value; do
    # Saltar líneas vacías y comentarios
    if [[ -n "$key" && ! "$key" =~ ^[[:space:]]*# ]]; then
        echo "🔧 Configurando $key..."
        
        # Usar AWS CLI para configurar la variable de entorno
        aws elasticbeanstalk update-environment \
            --environment-name cldci-staging-env \
            --option-settings Namespace=aws:elasticbeanstalk:application:environment,OptionName="$key",Value="$value" \
            --region us-east-1
    fi
done < .env.staging

echo ""
echo "✅ Variables de entorno configuradas correctamente"
echo "🌐 Aplicación disponible en: http://cldci-staging-env.eba-xphp7eqe.us-east-1.elasticbeanstalk.com"
echo ""
echo "📊 Para verificar las variables configuradas:"
echo "aws elasticbeanstalk describe-configuration-settings --environment-name cldci-staging-env --region us-east-1"
