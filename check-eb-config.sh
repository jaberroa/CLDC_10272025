#!/bin/bash

echo "🔍 Verificando configuración de Elastic Beanstalk..."

# Verificar que AWS CLI está disponible
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI no está instalado"
    echo "Instala con: sudo snap install aws-cli"
    exit 1
fi

echo "📊 Información del entorno cldci-staging-env:"
echo "=============================================="

# Obtener información del entorno
aws elasticbeanstalk describe-environments \
    --environment-names cldci-staging-env \
    --region us-east-1 \
    --query 'Environments[0].{Name:EnvironmentName,Status:Status,Health:Health,Platform:PlatformArn}' \
    --output table

echo ""
echo "🔧 Configuración actual:"
echo "========================"

# Obtener configuración del entorno
aws elasticbeanstalk describe-configuration-settings \
    --application-name cldci-staging \
    --environment-name cldci-staging-env \
    --region us-east-1 \
    --query 'ConfigurationSettings[0].OptionSettings[?Namespace==`aws:elasticbeanstalk:application:environment`]' \
    --output table

echo ""
echo "📋 Versiones de aplicación:"
echo "==========================="

# Listar versiones de aplicación
aws elasticbeanstalk describe-application-versions \
    --application-name cldci-staging \
    --region us-east-1 \
    --query 'ApplicationVersions[].{Version:VersionLabel,Created:DateCreated,Status:Status}' \
    --output table

echo ""
echo "🚀 Para corregir el problema:"
echo "============================"
echo "1. Verifica que la plataforma sea 'Docker running on 64bit Amazon Linux 2023'"
echo "2. Asegúrate de que no haya una aplicación 'Sample' instalada"
echo "3. Configura las variables de entorno necesarias"
echo "4. Si es necesario, recrea el entorno con la configuración correcta"
