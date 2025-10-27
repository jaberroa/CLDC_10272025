#!/bin/bash

# Script de deploy para Render
# Este script se ejecuta durante el build en Render

echo "🚀 Iniciando deploy de CLDCI en Render..."

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

echo "✅ Directorio correcto detectado"

# Verificar que Node.js esté instalado
if ! command -v node &> /dev/null; then
    echo "❌ Error: Node.js no está instalado"
    exit 1
fi

echo "✅ Node.js $(node --version) detectado"

# Verificar que npm esté instalado
if ! command -v npm &> /dev/null; then
    echo "❌ Error: npm no está instalado"
    exit 1
fi

echo "✅ npm $(npm --version) detectado"

# Instalar dependencias de Node.js
echo "📦 Instalando dependencias de Node.js..."
npm ci --only=production

if [ $? -ne 0 ]; then
    echo "❌ Error: Falló la instalación de dependencias de Node.js"
    exit 1
fi

echo "✅ Dependencias de Node.js instaladas"

# Construir assets con Vite
echo "🔨 Construyendo assets con Vite..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Error: Falló la construcción de assets"
    exit 1
fi

echo "✅ Assets construidos exitosamente"

# Verificar que los archivos CSS se generaron
if [ ! -d "public/build/assets" ]; then
    echo "❌ Error: Directorio public/build/assets no existe"
    exit 1
fi

echo "✅ Directorio de assets verificado"

# Listar archivos CSS generados
echo "📋 Archivos CSS generados:"
ls -la public/build/assets/*.css

echo "🎉 Deploy completado exitosamente!"
