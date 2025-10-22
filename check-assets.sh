#!/bin/bash
# Verificar que todos los assets están disponibles

echo "🔍 Verificando assets..."

# Verificar assets estáticos
echo "📁 Verificando assets estáticos..."
curl -s -o /dev/null -w "CSS: %{http_code}\n" http://localhost:8000/assets/css/app.css
curl -s -o /dev/null -w "JS: %{http_code}\n" http://localhost:8000/assets/js/app.js

# Verificar assets compilados
echo "📦 Verificando assets compilados..."
curl -s -o /dev/null -w "Manifest: %{http_code}\n" http://localhost:8000/build/manifest.json

# Verificar configuración
echo "⚙️ Verificando configuración..."
docker-compose exec app php artisan config:show app.url

echo "✅ Verificación completada"

