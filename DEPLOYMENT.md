# 🚀 Guía de Deployment - Laravel 12 + Docker + AWS Elastic Beanstalk

## 📋 Configuración Completa

### Archivos Creados

- `.github/workflows/deploy.yml` - GitHub Actions para deploy automático
- `.elasticbeanstalk/config.yml` - Configuración de Elastic Beanstalk
- `Dockerrun.aws.json` - Configuración de Docker para Elastic Beanstalk
- `.ebextensions/` - Configuraciones específicas de Elastic Beanstalk
- `Dockerfile.eb` - Dockerfile optimizado para Elastic Beanstalk
- `deploy-local.sh` - Script para probar deployment localmente

## 🔧 Configuración de GitHub Secrets

Necesitas configurar estos secrets en tu repositorio de GitHub:

1. Ve a tu repositorio en GitHub
2. Settings → Secrets and variables → Actions
3. Agrega estos secrets:

```
AWS_ACCESS_KEY_ID=tu_access_key_aqui
AWS_SECRET_ACCESS_KEY=tu_secret_key_aqui
```

## 📝 Variables de Entorno

### Para Staging (.env.staging)
```bash
APP_NAME="CLDCI - Staging"
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
```

### Para Local (.env.local)
```bash
APP_NAME="CLDC Local"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cldc_database
DB_USERNAME=cldc_user
DB_PASSWORD=cldc_password
```

## 🧪 Probar Deployment Localmente

### Opción 1: Usar el script automatizado
```bash
./deploy-local.sh
cd deploy-package
docker build -t cldc-test .
docker run -p 8080:80 cldc-test
```

### Opción 2: Probar con Docker Compose (desarrollo)
```bash
docker-compose up -d
```

## 🚀 Deployment a AWS

### Automático (Recomendado)
1. Haz push a la rama `main`:
```bash
git add .
git commit -m "Deploy to staging"
git push origin main
```

2. GitHub Actions se ejecutará automáticamente y desplegará a AWS

### Manual (Si es necesario)
```bash
# Instalar EB CLI
pip install awsebcli

# Inicializar (solo la primera vez)
eb init

# Desplegar
eb deploy
```

## 📊 Monitoreo y Logs

### Ver logs en GitHub Actions
1. Ve a tu repositorio en GitHub
2. Actions → Deploy to AWS Elastic Beanstalk
3. Haz clic en el workflow ejecutado

### Ver logs en AWS
1. Ve a AWS Console → Elastic Beanstalk
2. Selecciona tu entorno `cldci-staging-env`
3. Logs → Request logs

## 🔍 Troubleshooting

### Problemas Comunes

1. **Error de permisos en GitHub Actions**
   - Verifica que los AWS Secrets estén configurados correctamente

2. **Error de conexión a la base de datos**
   - Verifica que las credenciales en `.env.staging` sean correctas
   - Asegúrate de que el RDS esté en la misma VPC que Elastic Beanstalk

3. **Error de migraciones**
   - Revisa los logs de Elastic Beanstalk
   - Verifica que el usuario de DB tenga permisos de migración

4. **Error de Docker build**
   - Prueba localmente con `deploy-local.sh`
   - Verifica que el Dockerfile sea válido

### Comandos Útiles

```bash
# Ver logs de GitHub Actions
gh run list
gh run view [run-id]

# Ver logs de Elastic Beanstalk
eb logs

# Verificar estado del entorno
eb status

# Abrir aplicación en el navegador
eb open
```

## 📈 Flujo de Trabajo

1. **Desarrollo Local**
   - Usa `.env.local` para desarrollo
   - Prueba con `docker-compose up`

2. **Testing**
   - Prueba deployment local con `deploy-local.sh`

3. **Deploy a Staging**
   - Push a `main` → GitHub Actions → AWS Elastic Beanstalk
   - Cliente puede ver cambios en: http://cldci-staging-env.eba-xphp7eqe.us-east-1.elasticbeanstalk.com

4. **Producción**
   - Una vez terminado, migrar a VPS para producción

## 🎯 URLs Importantes

- **Staging**: http://cldci-staging-env.eba-xphp7eqe.us-east-1.elasticbeanstalk.com
- **GitHub Actions**: https://github.com/jaberroa/cldc_new/actions
- **AWS Console**: https://console.aws.amazon.com/elasticbeanstalk/home

## ✅ Checklist de Deployment

- [ ] GitHub Secrets configurados
- [ ] Variables de entorno correctas
- [ ] Base de datos Aurora RDS accesible
- [ ] Elastic Beanstalk funcionando
- [ ] GitHub Actions ejecutándose
- [ ] Aplicación accesible en staging
- [ ] Logs funcionando correctamente
