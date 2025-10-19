# Estructura del Proyecto CLDCI

## 📁 Organización de Carpetas

### **Backend (Laravel)**
```
app/
├── Http/Controllers/
│   ├── Auth/                    # Controladores de autenticación
│   │   ├── AuthenticatedSessionController.php
│   │   ├── RegisterController.php
│   │   └── PasswordResetController.php
│   ├── API/                     # Controladores API
│   │   ├── MiembrosApiController.php
│   │   └── OrganizacionesApiController.php
│   └── DashboardController.php
├── Models/                      # Modelos Eloquent
│   ├── Miembro.php
│   ├── Organizacion.php
│   └── Asamblea.php
├── Services/                    # Lógica de negocio
│   ├── MiembroService.php
│   └── EleccionService.php
└── Middleware/                  # Middleware personalizado
    ├── CheckRole.php
    └── CheckOrganization.php
```

### **Frontend (Blade Templates)**
```
resources/views/
├── auth/                        # Vistas de autenticación
│   ├── signin.blade.php         # Login principal
│   ├── signup.blade.php         # Registro
│   ├── forgot-password.blade.php # Recuperar contraseña
│   ├── reset-password.blade.php  # Restablecer contraseña
│   ├── email-verify.blade.php   # Verificación de email
│   ├── signout.blade.php        # Cerrar sesión
│   └── static/                  # Vistas estáticas de referencia
│       ├── signin.blade.php
│       └── signup.blade.php
├── partials/                    # Componentes reutilizables
│   ├── layouts/
│   │   └── master.blade.php     # Layout principal
│   ├── header.blade.php
│   ├── sidebar.blade.php
│   └── footer.blade.php
├── components/                  # Componentes Blade
│   ├── stat-card.blade.php
│   ├── data-table.blade.php
│   └── filter-panel.blade.php
├── dashboard.blade.php          # Dashboard principal
├── miembros/
│   ├── index.blade.php          # Lista de miembros
│   ├── show.blade.php           # Detalle de miembro
│   └── carnet.blade.php         # Carnet digital
└── directiva/
    ├── index.blade.php          # Estructura directiva
    └── organigrama.blade.php    # Organigrama
```

### **Assets Públicos**
```
public/
├── assets/                      # Assets de Urbix
│   ├── css/
│   ├── js/
│   ├── images/
│   └── libs/
├── auth/                        # Archivos HTML estáticos de referencia
│   ├── auth-signin.html
│   ├── auth-signup.html
│   ├── auth-forgot-password.html
│   ├── auth-reset-password.html
│   ├── auth-email-verify.html
│   └── auth-signout.html
└── storage/                     # Archivos subidos
    └── expedientes/             # Documentos legales
```

### **Base de Datos**
```
database/
├── migrations/
│   ├── 2025_01_19_000001_create_cldci_schema.php
│   └── 2025_01_19_000002_add_user_roles.php
├── seeders/
│   ├── CldciDataSeeder.php
│   └── UserRoleSeeder.php
└── factories/
    ├── MiembroFactory.php
    └── OrganizacionFactory.php
```

### **Configuración**
```
routes/
├── web.php                      # Rutas web principales
├── api.php                      # Rutas API
└── auth.php                     # Rutas de autenticación

config/
├── database.php                 # Configuración MySQL
├── filesystems.php              # Storage expedientes
└── auth.php                     # Configuración autenticación
```

## 🎯 **Mejores Prácticas Implementadas**

### **1. Separación de Responsabilidades**
- **Controladores**: Solo lógica de presentación
- **Servicios**: Lógica de negocio compleja
- **Modelos**: Relaciones y validaciones
- **Middleware**: Autenticación y autorización

### **2. Estructura de Vistas**
- **Layouts**: Reutilización de diseño
- **Componentes**: Elementos reutilizables
- **Partials**: Fragmentos de código
- **Auth**: Vistas de autenticación organizadas

### **3. Organización de Assets**
- **Urbix**: Assets del template en `public/assets/`
- **Auth**: Archivos HTML de referencia en `public/auth/`
- **Storage**: Archivos subidos organizados por módulo

### **4. Rutas Organizadas**
- **Web**: Rutas principales de la aplicación
- **API**: Endpoints JSON para AJAX
- **Auth**: Rutas de autenticación separadas

## 📋 **Estructura de Archivos por Módulo**

### **Módulo de Autenticación**
```
auth/
├── Controllers/
│   ├── AuthenticatedSessionController.php
│   ├── RegisterController.php
│   └── PasswordResetController.php
├── Views/
│   ├── signin.blade.php
│   ├── signup.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   ├── email-verify.blade.php
│   └── signout.blade.php
├── Middleware/
│   ├── CheckRole.php
│   └── CheckOrganization.php
└── Routes/
    └── auth.php
```

### **Módulo de Miembros**
```
miembros/
├── Controllers/
│   ├── MiembrosController.php
│   └── MiembrosApiController.php
├── Models/
│   └── Miembro.php
├── Services/
│   └── MiembroService.php
├── Views/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── carnet.blade.php
└── Routes/
    └── miembros.php
```

## 🔧 **Configuración de Desarrollo**

### **Variables de Entorno**
```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cldc_database
DB_USERNAME=cldc_user
DB_PASSWORD=cldc_password

# Storage
FILESYSTEM_DISK=local
EXPEDIENTES_DISK=expedientes
```

### **Comandos de Desarrollo**
```bash
# Iniciar contenedores
docker-compose up -d

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Iniciar servidor
php artisan serve
```

## 📚 **Documentación Adicional**

- [Guía de Desarrollo](docs/DEVELOPMENT.md)
- [API Documentation](docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Troubleshooting](docs/TROUBLESHOOTING.md)

---

**CLDCI - Sistema de Gestión Institucional**  
*Estructura organizada para facilitar el mantenimiento y escalabilidad*
