# 🔧 Guía para Recrear el Entorno de Elastic Beanstalk

## 📋 Pasos para recrear el entorno (OPCIÓN 1)

### 1. Eliminar el entorno actual
1. Ve a AWS Console > Elastic Beanstalk > cldci-staging-env
2. Haz clic en "Actions" > "Terminate environment"
3. Confirma la eliminación
4. Espera a que se complete (5-10 minutos)

### 2. Crear nuevo entorno
1. Ve a AWS Console > Elastic Beanstalk > Applications > cldci-staging
2. Haz clic en "Create environment"
3. Selecciona "Web server environment"
4. Configura:
   - **Environment name**: `cldci-staging-env`
   - **Platform**: Docker
   - **Platform branch**: Docker running on 64bit Amazon Linux 2023
   - **Platform version**: Latest
   - **Application code**: Upload your code
   - **Source**: Upload file (usar el deployment-package.zip del último GitHub Actions)

### 3. Configurar variables de entorno
Una vez creado el entorno, ve a:
1. Configuration > Software > Environment properties
2. Agregar las siguientes variables:

```
APP_NAME=CLDCI - Staging
APP_ENV=staging
APP_KEY=base64:iJKg0rdmQQB7NiVSVrUN1psj3t5eRmz/AFzBexYU
APP_DEBUG=false
APP_URL=https://staging.cldc.org.do

DB_CONNECTION=mysql
DB_HOST=cldci-staging.c4rie2uost3w.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=cldci_staging
DB_USERNAME=cldci_user
DB_PASSWORD=2192Daa6251981*.*

LOG_CHANNEL=stack
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@cldc.org.do
MAIL_FROM_NAME=CLDCI
```

### 4. Verificar funcionamiento
1. Esperar a que el entorno esté "Healthy"
2. Probar la aplicación: http://cldci-staging-env.eba-xphp7eqe.us-east-1.elasticbeanstalk.com
3. Verificar que no haya errores en los logs

## 🎯 Ventajas de recrear el entorno:
- ✅ Elimina la aplicación "Sample" problemática
- ✅ Configuración limpia desde el inicio
- ✅ Evita problemas futuros de compatibilidad
- ✅ Asegura que el entorno esté optimizado para Laravel + Docker
