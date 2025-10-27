# Módulo de Gestión Documental - Guía de Instalación y Uso

## 📦 Instalación

### Prerrequisitos
- ✅ PHP 8.1 o superior
- ✅ Laravel 11.x
- ✅ MySQL 8.0 o superior
- ✅ Composer
- ✅ Node.js y NPM (para assets)

### Paso 1: Ejecutar Migraciones

```bash
# Ejecutar todas las migraciones del módulo
php artisan migrate

# Verificar que se crearon las 12 migraciones
# ✅ secciones_documentales
# ✅ carpetas_documentales
# ✅ documentos_gestion
# ✅ metadatos_documentales
# ✅ versiones_documentos
# ✅ comparticion_documentos
# ✅ aprobaciones_documentos
# ✅ firmas_electronicas
# ✅ auditoria_documentos
# ✅ recordatorios_documentos
# ✅ permisos_documentales
# ✅ comentarios_documentos
```

### Paso 2: Crear Directorio de Storage

```bash
# Crear directorio para almacenar documentos
php artisan storage:link

# Verificar que se creó el enlace simbólico
ls -la public/storage
```

### Paso 3: Configurar Permisos de Archivos

```bash
# Dar permisos al directorio de storage
chmod -R 775 storage
chmod -R 775 public/storage

# Asignar propietario correcto (según tu servidor)
chown -R www-data:www-data storage
chown -R www-data:www-data public/storage
```

### Paso 4: Publicar Assets (Opcional)

```bash
# Si tienes assets del módulo para publicar
php artisan vendor:publish --tag=gestion-documental

# Compilar assets
npm run build
```

## ⚙️ Configuración

### 1. Configurar Roles de Usuario

El sistema utiliza los siguientes roles (debes tener estos roles en tu tabla `users`):

```php
// En tu migración o seeder de usuarios
'rol' => 'superadmin'      // Acceso total
'rol' => 'administrador'   // Gestión completa
'rol' => 'coordinador'     // Aprobaciones y compartir
'rol' => 'secretario'      // Editar y compartir
'rol' => 'tesorero'        // Ver y crear
'rol' => 'miembro'         // Solo ver
```

### 2. Configurar Storage

En `config/filesystems.php`, asegúrate de tener el disco público:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### 3. Variables de Entorno

Agrega al `.env`:

```bash
# Gestión Documental
DOCUMENTAL_MAX_FILE_SIZE=51200  # En KB (50MB)
DOCUMENTAL_ALLOWED_EXTENSIONS=pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif
DOCUMENTAL_STORAGE_DISK=public
```

### 4. Crear Secciones Iniciales (Seeder)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeccionDocumental;

class SeccionesDocumentalesSeeder extends Seeder
{
    public function run()
    {
        $secciones = [
            [
                'nombre' => 'Contratos',
                'slug' => 'contratos',
                'descripcion' => 'Contratos de servicios y convenios',
                'icono' => 'ri-file-text-line',
                'color' => '#0d6efd',
                'orden' => 1,
                'activa' => true,
                'requiere_aprobacion' => true,
                'permite_versionado' => true,
            ],
            [
                'nombre' => 'Actas',
                'slug' => 'actas',
                'descripcion' => 'Actas de asambleas y reuniones',
                'icono' => 'ri-file-list-line',
                'color' => '#198754',
                'orden' => 2,
                'activa' => true,
            ],
            [
                'nombre' => 'Documentos Administrativos',
                'slug' => 'administrativos',
                'descripcion' => 'Documentos administrativos internos',
                'icono' => 'ri-briefcase-line',
                'color' => '#ffc107',
                'orden' => 3,
                'activa' => true,
            ],
            [
                'nombre' => 'Documentos Legales',
                'slug' => 'legales',
                'descripcion' => 'Documentos con valor legal',
                'icono' => 'ri-scales-line',
                'color' => '#dc3545',
                'orden' => 4,
                'activa' => true,
                'requiere_aprobacion' => true,
                'requiere_firma' => true,
            ],
        ];

        foreach ($secciones as $seccion) {
            SeccionDocumental::create($seccion);
        }
    }
}
```

Ejecutar:
```bash
php artisan db:seed --class=SeccionesDocumentalesSeeder
```

## 🚀 Uso del Módulo

### Acceso al Módulo

1. **Menú Principal**:
   - En el sidebar, buscar "Gestión Documental"
   - Desplegará 6 opciones principales

2. **Dashboard**:
   - URL: `/gestion-documental`
   - Vista general con estadísticas
   - Acciones rápidas
   - Documentos recientes

### Gestionar Secciones

**Roles permitidos**: Superadmin, Administrador

1. Ir a `Gestión Documental > Secciones`
2. Clic en "Nueva Sección"
3. Completar:
   - Nombre
   - Descripción
   - Icono (Remix Icon)
   - Color
   - Configuraciones (aprobación, versionado, etc.)
4. Guardar

### Crear Carpetas

**Roles permitidos**: Superadmin, Admin, Coordinador, Secretario

1. Ir a `Gestión Documental > Mis Documentos`
2. Clic en "Nueva Carpeta"
3. Seleccionar sección padre
4. Nombre y descripción
5. Configurar si es pública o privada
6. Guardar

### Subir Documentos

**Roles permitidos**: Superadmin, Admin, Coordinador, Secretario, Tesorero

1. Ir a `Gestión Documental > Mis Documentos`
2. Clic en "Subir Documento"
3. **Drag & Drop** o seleccionar archivo
4. Completar metadatos:
   - Título
   - Descripción
   - Estado (Borrador/Revisión/Aprobado)
   - Nivel de acceso
   - Confidencialidad
5. Seleccionar ubicación (Sección/Carpeta)
6. Opciones adicionales (fecha vencimiento)
7. Clic en "Subir Documento"

### Buscar Documentos

**Todos los usuarios**

1. Ir a `Gestión Documental > Buscar Documentos`
2. Ingresar término de búsqueda
3. **Filtros avanzados** (opcional):
   - Sección
   - Formato
   - Rango de fechas
4. Ver resultados con preview

### Aprobar Documentos

**Roles permitidos**: Superadmin, Admin, Coordinador

1. Ir a `Gestión Documental > Mis Aprobaciones`
2. Ver lista de pendientes
3. Clic en documento para revisar
4. **Aprobar**:
   - Botón "Aprobar"
   - Comentarios opcionales
   - Confirmar
5. **Rechazar**:
   - Botón "Rechazar"
   - Razón obligatoria
   - Confirmar

### Compartir Documentos

**Roles permitidos**: Superadmin, Admin, Coordinador, Secretario

1. Abrir documento
2. Clic en "Compartir"
3. Seleccionar tipo:
   - **Interno**: Usuario del sistema
   - **Externo**: Email
   - **Público**: Link abierto
4. Configurar permisos:
   - Puede descargar
   - Puede comentar
5. Opciones de seguridad:
   - Fecha de expiración
   - Contraseña
   - Límite de accesos
6. Enviar

### Solicitar Firma Electrónica

**Roles permitidos**: Superadmin, Admin, Coordinador, Secretario

1. Abrir documento
2. Clic en "Solicitar Firma"
3. Agregar firmantes:
   - Internos (usuarios)
   - Externos (email + nombre)
4. Configurar:
   - Tipo (Secuencial/Paralelo)
   - Fecha límite
   - Mensaje
5. Enviar solicitud

### Ver Auditoría

**Roles permitidos**: Superadmin, Administrador

1. Abrir documento
2. Pestaña "Auditoría"
3. Ver historial completo:
   - Quién accedió
   - Qué hizo
   - Cuándo
   - Desde dónde (IP)

## 🔒 Permisos y Seguridad

### Matriz de Permisos por Rol

| Acción | Superadmin | Admin | Coordinador | Secretario | Tesorero | Miembro |
|--------|-----------|-------|-------------|-----------|----------|---------|
| Ver documentos | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Crear documentos | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Editar documentos | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Eliminar documentos | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Compartir | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Aprobar | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Gestionar secciones | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Gestionar carpetas | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |

### Niveles de Acceso a Documentos

| Nivel | Descripción | Quién puede ver |
|-------|-------------|----------------|
| **Público** | Acceso sin restricciones | Todos |
| **Interno** | Solo usuarios autenticados | Usuarios del sistema |
| **Confidencial** | Restringido a roles altos | Superadmin, Admin, Coordinador, Secretario |
| **Restringido** | Permisos explícitos | Superadmin, Admin, Creador, Usuarios con permiso |

## 🛠️ Mantenimiento

### Limpiar Archivos Temporales

```bash
# Limpiar archivos de más de 30 días en papelera
php artisan documental:clean-trash --days=30

# Limpiar comparticiones expiradas
php artisan documental:clean-expired-shares
```

### Backup de Documentos

```bash
# Backup de archivos
tar -czf documentos-backup-$(date +%Y%m%d).tar.gz storage/app/public/documentos_gestion/

# Backup de base de datos (solo tablas documentales)
mysqldump -u usuario -p base_datos \
  secciones_documentales \
  carpetas_documentales \
  documentos_gestion \
  versiones_documentos \
  > documentos-db-backup-$(date +%Y%m%d).sql
```

### Actualizar Estadísticas

```bash
# Recalcular estadísticas de carpetas
php artisan documental:update-stats
```

## 📊 Monitoreo

### Logs Importantes

```bash
# Ver logs de auditoría documental
tail -f storage/logs/laravel.log | grep "AuditoriaDocumento"

# Ver errores de subida
tail -f storage/logs/laravel.log | grep "DocumentoGestion"
```

### Alertas Recomendadas

- ❗ Documentos sin aprobar > 7 días
- ❗ Firmas pendientes próximas a vencer
- ❗ Documentos próximos a vencer
- ❗ Intentos de acceso no autorizado
- ❗ Espacio en disco < 10%

## 🆘 Troubleshooting

### Problema: No se suben archivos

**Solución**:
```bash
# Verificar permisos
ls -la storage/app/public

# Verificar límite de PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Aumentar límites en php.ini
upload_max_filesize = 50M
post_max_size = 50M
```

### Problema: Error 403 al acceder

**Solución**:
1. Verificar rol del usuario
2. Verificar que el middleware está registrado en `bootstrap/app.php`
3. Limpiar cache de rutas:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Problema: Preview no funciona

**Solución**:
1. Verificar enlace simbólico: `php artisan storage:link`
2. Verificar permisos de archivos
3. Verificar URL en `.env` (APP_URL)

## 📞 Soporte

Para reportar problemas o solicitar mejoras:

1. **Documentación**: `/docs/gestion-documental/`
2. **Logs**: `storage/logs/laravel.log`
3. **Auditoría**: Tabla `auditoria_documentos`

---

**Versión**: 1.0.0-alpha
**Última actualización**: 2025-10-25
**Estado**: ✅ Producción Ready

