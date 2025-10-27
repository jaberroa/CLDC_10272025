# GitHub Secrets necesarios para Render

## Configuración en GitHub:
1. Ve a tu repositorio en GitHub
2. Settings → Secrets and variables → Actions
3. Agrega estos secrets:

### RENDER_SERVICE_ID
- Ve a tu servicio en Render
- En la URL: https://dashboard.render.com/web/[SERVICE_ID]
- Copia el SERVICE_ID de la URL

### RENDER_API_KEY
- Ve a tu perfil en Render
- Account Settings → API Keys
- Genera una nueva API Key
- Copia la clave generada

## Variables de entorno en Render:
Ya configuradas en render.yaml:
- APP_ENV=production
- APP_DEBUG=false
- DB_CONNECTION=pgsql
- CACHE_DRIVER=file
- SESSION_DRIVER=file
- QUEUE_CONNECTION=sync
- LOG_CHANNEL=stack
- LOG_LEVEL=error

## Variables que debes configurar manualmente en Render:
- APP_KEY (generar en Render)
- APP_URL=https://app-cldc.onrender.com
- DB_HOST=dpg-d3vhlceuk2gs73ejek1g-a.oregon-postgres.render.com
- DB_PORT=5432
- DB_DATABASE=cldc_database
- DB_USERNAME=cldc_database_user
- DB_PASSWORD=3q64va5xXVXYjJ5RCu9NliJrgAAAFwBM
