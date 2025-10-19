# CLDCI - Sistema de Gestión Institucional

Sistema de gestión integral para el Círculo de Locutores Dominicanos Colegiados (CLDCI), desarrollado con Laravel y la plantilla Urbix.

## 🚀 Características Principales

### 📊 Dashboard Inteligente
- Estadísticas en tiempo real
- Gráficos interactivos con ApexCharts
- Próximas asambleas y eventos
- Accesos rápidos a módulos

### 👥 Gestión de Miembros
- Registro completo de miembros
- Filtros avanzados por tipo y estado
- Carnet digital con código QR
- Historial de actividades
- Exportación a CSV

### 🏛️ Estructura Directiva
- Organigrama interactivo
- Gestión de cargos y responsabilidades
- Timeline de cambios
- Jerarquía organizacional

### 📋 Módulos Adicionales
- **Elecciones**: Sistema de votación digital
- **Formación**: Gestión de cursos y capacitaciones
- **Asambleas**: Control de asistencia y quorum
- **Reportes**: Análisis y estadísticas
- **Transparencia**: Documentos públicos
- **Integraciones**: APIs y servicios externos

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Urbix UI
- **Base de Datos**: MySQL 8.0
- **Contenedores**: Docker + Docker Compose
- **Deploy**: AWS Elastic Beanstalk
- **Assets**: Bootstrap 5, ApexCharts, Select2

## 📦 Instalación Rápida

### Opción 1: Script Automático
```bash
./setup-cldci.sh
```

### Opción 2: Manual
```bash
# 1. Clonar repositorio
git clone <repository-url>
cd cldc_new

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos
php artisan migrate
php artisan db:seed

# 5. Iniciar servidor
php artisan serve
```

### Opción 3: Docker
```bash
# Iniciar contenedores
docker-compose up -d

# Ejecutar migraciones
docker exec -it cldc_app php artisan migrate
docker exec -it cldc_app php artisan db:seed
```

## 🌐 Acceso al Sistema

- **URL Local**: http://localhost:8000
- **URL Docker**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080

## 🔐 Credenciales de Acceso

### Usuario Administrador
- **Email**: admin@cldci.org.do
- **Contraseña**: password

### Datos de Demostración
- 5 miembros de ejemplo
- 32 seccionales provinciales
- 8 seccionales internacionales
- Cursos y asambleas programadas

## 📊 Estructura de Base de Datos

### Tablas Principales
- `organizaciones` - CLDCI y seccionales
- `miembros` - Registro de miembros
- `asambleas` - Gestión de asambleas
- `cursos` - Capacitaciones
- `elecciones` - Procesos electorales
- `user_roles` - Roles y permisos

### Relaciones Clave
- Miembros → Organizaciones
- Asambleas → Asistencia
- Cursos → Inscripciones
- Elecciones → Candidatos → Votos

## 🎨 Diseño y UI/UX

### Plantilla Urbix
- Diseño moderno y responsive
- Componentes reutilizables
- Iconografía Remix Icons
- Paleta de colores consistente

### Componentes Principales
- Cards estadísticas
- Tablas con filtros
- Modales y offcanvas
- Gráficos interactivos
- Formularios validados

## 🔧 Configuración Avanzada

### Variables de Entorno
```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cldc_database
DB_USERNAME=cldc_user
DB_PASSWORD=cldc_password

# Email
MAIL_FROM_ADDRESS=noreply@cldci.org.do
MAIL_FROM_NAME="CLDCI"
```

### Docker Compose
```yaml
services:
  app:     # Laravel application
  nginx:   # Web server
  db:      # MySQL database
  phpmyadmin: # Database management
```

## 📈 Funcionalidades por Módulo

### Dashboard
- Estadísticas generales
- Gráficos de distribución
- Actividad reciente
- Accesos rápidos

### Miembros
- Listado con filtros
- Perfil detallado
- Carnet digital
- Historial de actividades
- Exportación de datos

### Directiva
- Organigrama interactivo
- Gestión de cargos
- Timeline de cambios
- Estructura jerárquica

## 🚀 Deploy a Producción

### AWS Elastic Beanstalk
```bash
# Configurar variables de entorno
./configure-env-aws.sh

# Deploy automático
git push origin main
```

### Variables de Producción
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_HOST=<rds-endpoint>`
- `MAIL_MAILER=smtp`

## 📝 API Endpoints

### Miembros
- `GET /api/miembros` - Listar miembros
- `GET /api/miembros/{id}` - Detalle de miembro
- `GET /api/miembros/estadisticas` - Estadísticas

### Dashboard
- `GET /api/dashboard/stats` - Estadísticas generales
- `GET /api/dashboard/graficos` - Datos para gráficos

### Directiva
- `GET /api/directiva/organigrama` - Estructura organizacional
- `GET /api/directiva/timeline` - Timeline de cambios

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Tests específicos
php artisan test --filter=MiembroTest
```

## 📚 Documentación Adicional

- [Guía de Usuario](docs/user-guide.md)
- [API Documentation](docs/api.md)
- [Deployment Guide](docs/deployment.md)
- [Troubleshooting](docs/troubleshooting.md)

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

- **Email**: soporte@cldci.org.do
- **Documentación**: [docs.cldci.org.do](https://docs.cldci.org.do)
- **Issues**: [GitHub Issues](https://github.com/cldci/issues)

---

**CLDCI - Sistema de Gestión Institucional v1.0**  
*Desarrollado con ❤️ para el Círculo de Locutores Dominicanos Colegiados*
