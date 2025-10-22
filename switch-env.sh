#!/bin/bash
# Script para cambiar entre entornos

case $1 in
    "local")
        cp .env.local .env
        echo "✅ Cambiado a entorno LOCAL"
        echo "🌐 URL: http://localhost:8000"
        ;;
    "ngrok")
        cp .env.ngrok .env
        echo "✅ Cambiado a entorno NGROK"
        echo "🌐 URL: https://isthmoid-restlessly-greta.ngrok-free.dev"
        ;;
    *)
        echo "❌ Uso: ./switch-env.sh [local|ngrok]"
        ;;
esac

# Recargar configuración
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
