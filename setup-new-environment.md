# 🚀 Configuración del Nuevo Entorno Elastic Beanstalk

## 📋 Pasos para crear el nuevo entorno

### 1. Crear nuevo entorno en AWS Console

**Configuración básica:**
- **Application name**: `cldci-staging`
- **Environment name**: `cldci-staging-env-new`
- **Platform**: Docker running on 64bit Amazon Linux 2023
- **Source**: Upload your code (usar el ZIP del último GitHub Actions)

### 2. Configurar variables de entorno

**Variables esenciales para Laravel:**
```
APP_NAME=CLDCI - Staging
APP_ENV=staging
APP_KEY=base64:iJKg0rdmQQB7NiVSVrUN1psj3t5eRmz/AFzBexYU
APP_DEBUG=false
APP_URL=https://staging.cldc.org.do

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=TU_RDS_ENDPOINT_AQUI
DB_PORT=3306
DB_DATABASE=cldci_staging
DB_USERNAME=cldci_user
DB_PASSWORD=2192Daa6251981*.*

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@cldc.org.do
MAIL_FROM_NAME=CLDCI

AWS_DEFAULT_REGION=us-east-1
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

### 3. Configuración de red y seguridad

**VPC y Subnets:**
- Usar la VPC predeterminada o crear una nueva
- Configurar subnets públicas para el Load Balancer
- Configurar subnets privadas para las instancias EC2

**Security Groups:**
- Permitir tráfico HTTP (80) y HTTPS (443) desde internet
- Permitir tráfico MySQL (3306) desde las instancias EC2 hacia RDS

### 4. Base de datos

**Aurora RDS:**
- **Engine**: Aurora MySQL
- **Database name**: `cldci_staging`
- **Username**: `cldci_user`
- **Password**: `2192Daa6251981*.*`
- **Endpoint**: Se usará en DB_HOST

### 5. Probar el deployment

Una vez creado el entorno:
1. Hacer commit y push del workflow actualizado
2. Verificar que GitHub Actions ejecute el deployment
3. Probar la aplicación en la nueva URL

## 🎯 Objetivos

- ✅ Nuevo entorno sin conflictos
- ✅ Variables de entorno configuradas correctamente
- ✅ Deployment automático funcionando
- ✅ Aplicación Laravel funcionando con RDS
