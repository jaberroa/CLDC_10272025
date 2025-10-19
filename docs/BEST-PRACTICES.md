# Mejores Prácticas - CLDCI

## 📁 **Estructura de Carpetas Organizada**

### **❌ Mala Práctica (Anterior)**
```
cldc_new/
├── auth-signin.html          # ❌ Archivos en raíz
├── auth-signup.html          # ❌ Archivos en raíz
├── auth-forgot-password.html # ❌ Archivos en raíz
├── auth-reset-password.html  # ❌ Archivos en raíz
├── auth-email-verify.html    # ❌ Archivos en raíz
└── auth-signout.html         # ❌ Archivos en raíz
```

### **✅ Buena Práctica (Actual)**
```
cldc_new/
├── resources/views/auth/     # ✅ Vistas Blade funcionales
│   ├── signin.blade.php
│   ├── signup.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   ├── email-verify.blade.php
│   └── signout.blade.php
├── public/auth/              # ✅ Archivos HTML de referencia
│   ├── auth-signin.html
│   ├── auth-signup.html
│   ├── auth-forgot-password.html
│   ├── auth-reset-password.html
│   ├── auth-email-verify.html
│   └── auth-signout.html
└── routes/
    ├── web.php               # ✅ Rutas principales
    ├── api.php               # ✅ Rutas API
    └── auth.php              # ✅ Rutas de autenticación
```

## 🎯 **Principios de Organización**

### **1. Separación de Responsabilidades**
- **Vistas Blade**: Funcionales, con lógica de Laravel
- **Archivos HTML**: Referencia/demo, estáticos
- **Rutas**: Organizadas por funcionalidad
- **Controladores**: Separados por módulo

### **2. Estructura de Carpetas**
```
app/
├── Http/Controllers/
│   ├── Auth/                 # Controladores de autenticación
│   ├── API/                  # Controladores API
│   └── DashboardController.php
├── Models/                   # Modelos Eloquent
├── Services/                 # Lógica de negocio
└── Middleware/               # Middleware personalizado

resources/views/
├── auth/                     # Vistas de autenticación
├── partials/                 # Componentes reutilizables
├── components/              # Componentes Blade
└── layouts/                  # Layouts principales

public/
├── assets/                   # Assets de Urbix
├── auth/                     # Archivos HTML estáticos
└── storage/                  # Archivos subidos

routes/
├── web.php                   # Rutas principales
├── api.php                   # Rutas API
└── auth.php                  # Rutas de autenticación
```

### **3. Organización de Rutas**
```php
// routes/web.php - Rutas principales
Route::get('/', function () {
    return redirect()->route('login');
});

// routes/auth.php - Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    // ... más rutas de autenticación
});

// routes/api.php - Rutas API
Route::prefix('api')->group(function () {
    Route::get('/miembros', [MiembrosApiController::class, 'index']);
    // ... más rutas API
});
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
├── HTML Static/
│   ├── auth-signin.html
│   ├── auth-signup.html
│   ├── auth-forgot-password.html
│   ├── auth-reset-password.html
│   ├── auth-email-verify.html
│   └── auth-signout.html
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

## 🚀 **Beneficios de la Nueva Estructura**

### **1. Mantenibilidad**
- ✅ Código organizado por funcionalidad
- ✅ Fácil localización de archivos
- ✅ Separación clara de responsabilidades

### **2. Escalabilidad**
- ✅ Fácil agregar nuevos módulos
- ✅ Estructura consistente
- ✅ Reutilización de componentes

### **3. Desarrollo**
- ✅ Fácil colaboración en equipo
- ✅ Estructura predecible
- ✅ Documentación clara

### **4. Producción**
- ✅ Deploy organizado
- ✅ Assets optimizados
- ✅ Rutas claras

## 📚 **Documentación Adicional**

- [Estructura del Proyecto](docs/STRUCTURE.md)
- [Guía de Desarrollo](docs/DEVELOPMENT.md)
- [API Documentation](docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)

---

**CLDCI - Sistema de Gestión Institucional**  
*Estructura organizada siguiendo las mejores prácticas de Laravel*
