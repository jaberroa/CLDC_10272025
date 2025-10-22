#!/bin/bash
# Script para arreglar permisos de herramientas de build

echo "🔧 Arreglando permisos de herramientas de build..."

# Arreglar permisos de vite
if [ -f "node_modules/.bin/vite" ]; then
    chmod +x node_modules/.bin/vite
    echo "✅ Permisos de vite arreglados"
fi

# Arreglar permisos de esbuild
if [ -f "node_modules/@esbuild/linux-x64/bin/esbuild" ]; then
    chmod +x node_modules/@esbuild/linux-x64/bin/esbuild
    echo "✅ Permisos de esbuild arreglados"
fi

# Arreglar permisos de otros binarios comunes
find node_modules/.bin -type f -name "*" -exec chmod +x {} \; 2>/dev/null || true

echo "✅ Permisos arreglados"