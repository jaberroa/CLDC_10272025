# 🚀 Guía de Desarrollo CLDCI

## Inicio Rápido

### Opción 1: Script Automático (Recomendado)
```bash
./start-dev.sh
```

### Opción 2: Manual
```bash
# Iniciar contenedores
docker-compose up -d

# Corregir permisos
./fix-permissions.sh

# Ejecutar migraciones
docker-compose exec app php artisan migrate
```

## URLs de Desarrollo

- **Aplicación**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
- **Logs**: `docker-compose exec app tail -f storage/logs/laravel.log`

## Comandos Útiles

### Gestión de Permisos
```bash
# Corregir permisos (si hay errores)
./fix-permissions.sh

# Verificar permisos
docker-compose exec app ls -la storage/logs/
```

### Base de Datos
```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Crear seeder
docker-compose exec app php artisan make:seeder NombreSeeder

# Ejecutar seeder
docker-compose exec app php artisan db:seed --class=NombreSeeder
```

### Cachés
```bash
# Limpiar todas las cachés
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Assets
```bash
# Compilar assets
npm run build

# Compilar en modo desarrollo
npm run dev
```

## Solución de Problemas

### Error: "Permission denied" en logs
```bash
./fix-permissions.sh
```

### Error: "Table doesn't exist"
```bash
docker-compose exec app php artisan migrate
```

### Error: "Class not found"
```bash
docker-compose exec app composer dump-autoload
```

### Error: Assets no cargan
```bash
npm run build
```

## Estructura del Proyecto

```
cldc_new/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── public/
│   └── assets/
├── docker-compose.yml
├── docker-compose.override.yml
├── fix-permissions.sh
└── start-dev.sh
```

## Scripts Disponibles

- `./start-dev.sh` - Inicia el entorno completo
- `./fix-permissions.sh` - Corrige permisos de archivos
- `npm run build` - Compila assets para producción
- `npm run dev` - Compila assets en modo desarrollo

## Notas Importantes

1. **Siempre usar `./fix-permissions.sh`** si hay errores de permisos
2. **No modificar** archivos de configuración Docker/EB
3. **Usar MySQL 8.0** para compatibilidad
4. **Assets se compilan** con Vite, no manualmente
5. **Logs se escriben** en `storage/logs/laravel.log`

## Contacto

Para problemas técnicos, revisar:
1. Logs de Laravel: `storage/logs/laravel.log`
2. Logs de Docker: `docker-compose logs app`
3. Permisos: `./fix-permissions.sh`


