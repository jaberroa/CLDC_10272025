#!/bin/bash

echo "🔍 Debugging database connection..."

# Verificar variables de entorno
echo "📋 Environment variables:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"
echo "APP_KEY: $APP_KEY"

# Verificar conexión a la base de datos
echo ""
echo "🔌 Testing database connection..."
if [ -n "$DB_HOST" ] && [ -n "$DB_USERNAME" ] && [ -n "$DB_PASSWORD" ]; then
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "✅ Database connection successful"
    else
        echo "❌ Database connection failed"
        echo "Testing connection details..."
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;"
    fi
else
    echo "❌ Missing database credentials"
fi

# Verificar Laravel configuration
echo ""
echo "🎯 Testing Laravel configuration..."
if [ -f "artisan" ]; then
    echo "✅ artisan file found"
    php artisan --version
    php artisan config:show database
else
    echo "❌ artisan file not found"
fi

echo ""
echo "🔧 Debugging completed"
