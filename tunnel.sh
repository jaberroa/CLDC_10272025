#!/bin/bash

# =======================================================
#  Script para iniciar el túnel Ngrok para Laravel
# =======================================================
# ============================
#  Túnel Ngrok para Laravel
# chmod +x tunnel.sh para dar permisos de ejecución
# ./tunnel.sh para iniciar el túnel
# ============================

# Verificar si Ngrok está instalado
if ! command -v ngrok &> /dev/null; then
  echo "❌ Ngrok no está instalado. Por favor, instálalo e intenta nuevamente."
  exit 1
fi

# Verificar si Docker está corriendo
if ! docker info > /dev/null 2>&1; then
  echo "⚠️ Docker no está corriendo. Iniciándolo..."
  sudo service docker start
  sleep 3
fi

# Verificar si el contenedor principal (nginx o app) está activo
if ! docker ps | grep -q "cldc_nginx"; then
  echo "🐳 Iniciando contenedores Docker..."
  docker compose up -d
  sleep 5
fi

# Iniciar el túnel Ngrok
echo "🚀 Iniciando túnel Ngrok al puerto 8000..."
ngrok http 8000
