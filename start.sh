#!/bin/bash

# Generar configuración de Nginx con el puerto correcto
PORT=${PORT:-8000}
sed "s/\${PORT:-8000}/$PORT/g" /etc/nginx/sites-available/default > /tmp/nginx.conf
mv /tmp/nginx.conf /etc/nginx/sites-available/default

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
