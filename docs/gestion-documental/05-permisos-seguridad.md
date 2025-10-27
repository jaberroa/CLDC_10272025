# Módulo de Gestión Documental - Permisos y Seguridad

## ✅ FASE 5 COMPLETADA: Sistema de Permisos y Seguridad

### Resumen

Se ha implementado un **sistema completo de permisos** con middleware personalizado, policies de Laravel, y helper functions para facilitar la verificación de permisos en todo el sistema.

## Componentes Implementados

### 1. Middleware: CheckDocumentalPermission ⭐

**Ubicación**: `app/Http/Middleware/CheckDocumentalPermission.php`

**Función**: Verifica permisos a nivel de ruta antes de ejecutar el controlador

**Permisos Soportados**:
```php
- documentos.ver          // Ver documentos
- documentos.crear        // Crear documentos
- documentos.editar       // Editar documentos
- documentos.eliminar     // Eliminar documentos
- documentos.compartir    // Compartir documentos
- documentos.aprobar      // Aprobar documentos
- secciones.gestionar     // Gestionar secciones
- carpetas.gestionar      // Gestionar carpetas
```

**Uso en Rutas**:
```php
Route::get('documentos', [DocumentosController::class, 'index'])
    ->middleware('documental.permission:documentos.ver');

Route::post('documentos', [DocumentosController::class, 'store'])
    ->middleware('documental.permission:documentos.crear');
```

**Características**:
- ✅ Super admin bypass (siempre tiene acceso)
- ✅ Verificación por rol
- ✅ Respuesta 403 automática si no tiene permisos
- ✅ Mensajes de error claros

---

### 2. Policies de Laravel

#### DocumentoGestionPolicy ⭐
**Ubicación**: `app/Policies/DocumentoGestionPolicy.php`

**Métodos Implementados**:
```php
viewAny(User $user)                          // Listar documentos
view(User $user, DocumentoGestion $doc)      // Ver documento específico
create(User $user)                           // Crear documento
update(User $user, DocumentoGestion $doc)    // Editar documento
delete(User $user, DocumentoGestion $doc)    // Eliminar documento
restore(User $user, DocumentoGestion $doc)   // Restaurar documento
forceDelete(User $user, DocumentoGestion $doc) // Eliminar permanentemente
download(User $user, DocumentoGestion $doc)  // Descargar documento
share(User $user, DocumentoGestion $doc)     // Compartir documento
approve(User $user, DocumentoGestion $doc)   // Aprobar documento
move(User $user, DocumentoGestion $doc)      // Mover documento
duplicate(User $user, DocumentoGestion $doc) // Duplicar documento
```

**Lógica de Permisos por Nivel de Acceso**:

| Nivel Acceso | Quién puede ver |
|--------------|----------------|
| **Público** | Todos (incluso sin login) |
| **Interno** | Usuarios autenticados |
| **Confidencial** | Superadmin, Admin, Coordinador, Secretario |
| **Restringido** | Superadmin, Admin, Creador, Usuarios con permiso explícito |

**Reglas Especiales**:
- ✅ El creador puede editar si no está aprobado
- ✅ El creador puede eliminar si está en borrador
- ✅ No puede aprobar sus propios documentos
- ✅ Respeta permisos de compartición (puede_descargar)

---

#### SeccionDocumentalPolicy
**Ubicación**: `app/Policies/SeccionDocumentalPolicy.php`

**Permisos**:
- ✅ **Ver lista**: Todos
- ✅ **Ver detalle**: Todos (si está activa) o Admin
- ✅ **Crear/Editar/Eliminar**: Solo Admin

---

#### CarpetaDocumentalPolicy
**Ubicación**: `app/Policies/CarpetaDocumentalPolicy.php`

**Permisos**:
- ✅ **Ver lista**: Todos
- ✅ **Ver carpeta**:
  - Carpetas públicas: Todos
  - Carpetas privadas: Admin o creador
- ✅ **Crear**: Admin, Coordinador, Secretario
- ✅ **Editar**: Admin o creador (si no es solo lectura)
- ✅ **Eliminar**: Admin o creador

---

### 3. Helper: PermissionHelper

**Ubicación**: `app/Helpers/PermissionHelper.php`

**Métodos Disponibles**:

#### can(string $permission): bool
Verifica si el usuario actual tiene un permiso
```php
if (PermissionHelper::can('documentos.crear')) {
    // Mostrar botón de crear
}
```

#### getRolesPermissions(): array
Obtiene la matriz completa de roles y permisos
```php
$permisos = PermissionHelper::getRolesPermissions();
// ['superadmin' => [...], 'administrador' => [...], ...]
```

#### getRolePermissions(string $rol): array
Obtiene los permisos de un rol específico
```php
$permisos = PermissionHelper::getRolePermissions('coordinador');
// ['documentos.ver', 'documentos.crear', ...]
```

#### canAccessDocument($user, $documento): bool
Verifica acceso a un documento específico según nivel
```php
if (PermissionHelper::canAccessDocument(auth()->user(), $documento)) {
    // Mostrar documento
}
```

---

## Matriz de Permisos por Rol

### Superadmin
```
✅ documentos.ver
✅ documentos.crear
✅ documentos.editar
✅ documentos.eliminar
✅ documentos.compartir
✅ documentos.aprobar
✅ secciones.gestionar
✅ carpetas.gestionar
```

### Administrador
```
✅ documentos.ver
✅ documentos.crear
✅ documentos.editar
✅ documentos.eliminar
✅ documentos.compartir
✅ documentos.aprobar
✅ secciones.gestionar
✅ carpetas.gestionar
```

### Coordinador
```
✅ documentos.ver
✅ documentos.crear
✅ documentos.editar
✅ documentos.compartir
✅ documentos.aprobar
✅ carpetas.gestionar
❌ documentos.eliminar
❌ secciones.gestionar
```

### Secretario
```
✅ documentos.ver
✅ documentos.crear
✅ documentos.editar
✅ documentos.compartir
✅ carpetas.gestionar
❌ documentos.eliminar
❌ documentos.aprobar
❌ secciones.gestionar
```

### Tesorero
```
✅ documentos.ver
✅ documentos.crear
❌ documentos.editar
❌ documentos.eliminar
❌ documentos.compartir
❌ documentos.aprobar
❌ secciones.gestionar
❌ carpetas.gestionar
```

### Miembro
```
✅ documentos.ver
❌ documentos.crear
❌ documentos.editar
❌ documentos.eliminar
❌ documentos.compartir
❌ documentos.aprobar
❌ secciones.gestionar
❌ carpetas.gestionar
```

---

## Integración en Rutas

**Archivo**: `routes/gestion-documental.php`

Todas las rutas protegidas tienen middleware aplicado:

```php
// Dashboard - requiere ver documentos
Route::get('/', function () {
    return view('gestion-documental.dashboard');
})->middleware('documental.permission:documentos.ver');

// Secciones - requiere gestionar secciones
Route::resource('secciones', SeccionesController::class)
    ->middleware('documental.permission:secciones.gestionar');

// Carpetas - requiere gestionar carpetas
Route::resource('carpetas', CarpetasController::class)
    ->middleware('documental.permission:carpetas.gestionar');

// Documentos - diferentes permisos por acción
Route::get('documentos', ...)
    ->middleware('documental.permission:documentos.ver');
Route::post('documentos', ...)
    ->middleware('documental.permission:documentos.crear');
Route::put('documentos/{id}', ...)
    ->middleware('documental.permission:documentos.editar');
Route::delete('documentos/{id}', ...)
    ->middleware('documental.permission:documentos.eliminar');

// Compartir
Route::post('documentos/{id}/compartir', ...)
    ->middleware('documental.permission:documentos.compartir');

// Aprobar
Route::post('aprobaciones/{id}/aprobar', ...)
    ->middleware('documental.permission:documentos.aprobar');
```

---

## Uso en Controladores

### Autorización Automática con Policies

```php
class DocumentosGestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(DocumentoGestion::class, 'documento');
    }
    
    // Automáticamente verifica:
    // index() -> viewAny
    // show() -> view
    // create() -> create
    // store() -> create
    // edit() -> update
    // update() -> update
    // destroy() -> delete
}
```

### Autorización Manual

```php
public function compartir(Request $request, DocumentoGestion $documento)
{
    $this->authorize('share', $documento);
    
    // Lógica de compartir...
}

public function descargar(DocumentoGestion $documento)
{
    $this->authorize('download', $documento);
    
    return Storage::download($documento->ruta);
}
```

---

## Uso en Vistas Blade

### Directivas @can

```blade
@can('create', App\Models\DocumentoGestion::class)
    <a href="{{ route('gestion-documental.documentos.create') }}" class="btn btn-primary">
        Subir Documento
    </a>
@endcan

@can('update', $documento)
    <a href="{{ route('gestion-documental.documentos.edit', $documento) }}" class="btn btn-warning">
        Editar
    </a>
@endcan

@can('delete', $documento)
    <button onclick="eliminar({{ $documento->id }})" class="btn btn-danger">
        Eliminar
    </button>
@endcan

@can('share', $documento)
    <button onclick="compartir({{ $documento->id }})" class="btn btn-info">
        Compartir
    </button>
@endcan
```

### Helper Personalizado

```blade
@if(App\Helpers\PermissionHelper::can('documentos.crear'))
    <div class="quick-action">
        <a href="{{ route('gestion-documental.documentos.create') }}">
            Subir Documento
        </a>
    </div>
@endif

@if(App\Helpers\PermissionHelper::can('secciones.gestionar'))
    <li class="menu-item">
        <a href="{{ route('gestion-documental.secciones.index') }}">
            Gestionar Secciones
        </a>
    </li>
@endif
```

---

## Seguridad Adicional Implementada

### 1. Validación de Ownership
- El creador de un documento tiene permisos especiales
- No puede aprobar sus propios documentos
- Puede eliminar solo si está en borrador

### 2. Niveles de Acceso a Documentos
- **Público**: Sin restricciones
- **Interno**: Requiere autenticación
- **Confidencial**: Solo roles específicos
- **Restringido**: Permisos explícitos requeridos

### 3. Protección de Descarga
- Verifica permisos antes de descargar
- Respeta flag `puede_descargar` en comparticiones
- Registra en auditoría cada descarga

### 4. Compartición Controlada
- Solo usuarios autorizados pueden compartir
- Links con tokens únicos
- Expiración automática
- Límite de accesos
- Protección con contraseña opcional

### 5. Aprobaciones
- Flujo de aprobación configurable
- No puede aprobar sus propios documentos
- Registro de aprobadores y fechas
- Razón obligatoria en rechazos

---

## Casos de Uso Comunes

### Caso 1: Documento Confidencial
```php
$documento = DocumentoGestion::create([
    'titulo' => 'Contrato Confidencial',
    'nivel_acceso' => 'confidencial',
    'confidencial' => true,
    'subido_por' => auth()->id()
]);

// Solo pueden ver:
// - Superadmin
// - Administrador
// - Coordinador
// - Secretario
```

### Caso 2: Documento Restringido
```php
$documento = DocumentoGestion::create([
    'titulo' => 'Informe Estratégico',
    'nivel_acceso' => 'restringido',
    'subido_por' => auth()->id()
]);

// Dar permisos explícitos
PermisoUsuarioDocumento::create([
    'documento_id' => $documento->id,
    'usuario_id' => $usuario->id,
    'puede_ver' => true,
    'puede_descargar' => true,
    'puede_comentar' => false
]);
```

### Caso 3: Carpeta de Solo Lectura
```php
$carpeta = CarpetaDocumental::create([
    'nombre' => 'Archivo Histórico',
    'solo_lectura' => true,
    'publica' => false,
    'creado_por' => auth()->id()
]);

// Solo admin puede modificar
// Otros pueden ver pero no editar
```

---

## Respuestas de Error

### 401 - No Autenticado
```json
{
    "message": "No autenticado"
}
```

### 403 - No Autorizado
```json
{
    "message": "No tienes permisos para realizar esta acción"
}
```

### 403 - Policy Denied
```json
{
    "message": "This action is unauthorized."
}
```

---

## Testing de Permisos

### Test de Middleware
```php
public function test_admin_puede_gestionar_secciones()
{
    $admin = User::factory()->create(['rol' => 'administrador']);
    
    $response = $this->actingAs($admin)
        ->get('/gestion-documental/secciones');
    
    $response->assertOk();
}

public function test_miembro_no_puede_gestionar_secciones()
{
    $miembro = User::factory()->create(['rol' => 'miembro']);
    
    $response = $this->actingAs($miembro)
        ->get('/gestion-documental/secciones');
    
    $response->assertForbidden();
}
```

### Test de Policies
```php
public function test_creador_puede_editar_documento_borrador()
{
    $user = User::factory()->create();
    $documento = DocumentoGestion::factory()->create([
        'subido_por' => $user->id,
        'estado' => 'borrador'
    ]);
    
    $this->assertTrue($user->can('update', $documento));
}

public function test_creador_no_puede_editar_documento_aprobado()
{
    $user = User::factory()->create(['rol' => 'secretario']);
    $documento = DocumentoGestion::factory()->create([
        'subido_por' => $user->id,
        'estado' => 'aprobado'
    ]);
    
    $this->assertFalse($user->can('update', $documento));
}
```

---

## Mejores Prácticas

### 1. Siempre verificar permisos en múltiples capas
```
Ruta (Middleware) → Controlador (Policy) → Vista (@can)
```

### 2. Usar nombres descriptivos para permisos
```php
// ✅ Bueno
'documentos.crear', 'secciones.gestionar'

// ❌ Malo
'create_docs', 'manage'
```

### 3. Documentar permisos personalizados
```php
/**
 * Determina si el usuario puede exportar documentos
 * 
 * Requiere rol de coordinador o superior
 */
public function export(User $user): bool
{
    return in_array($user->rol, ['coordinador', 'administrador', 'superadmin']);
}
```

### 4. Registrar intentos de acceso no autorizado
```php
if (!$this->authorize('view', $documento)) {
    AuditoriaDocumento::registrar('intento_acceso_denegado', $documento, 'Acceso no autorizado');
    abort(403);
}
```

---

## Extensibilidad

### Agregar Nuevos Permisos

**1. En Middleware**:
```php
case 'documentos.exportar':
    return $this->canExportDocuments($user);
```

**2. En Policy**:
```php
public function export(User $user, DocumentoGestion $documento): bool
{
    return in_array($user->rol, ['coordinador', 'administrador', 'superadmin']);
}
```

**3. En Rutas**:
```php
Route::get('documentos/exportar', [DocumentosController::class, 'exportar'])
    ->middleware('documental.permission:documentos.exportar');
```

**4. En Helper**:
```php
'coordinador' => [
    // ... permisos existentes
    'documentos.exportar',
],
```

---

## Estadísticas

- **3 Policies** creadas
- **1 Middleware** personalizado
- **1 Helper** de permisos
- **8 permisos** definidos
- **6 roles** soportados
- **40+ rutas** protegidas
- **4 niveles** de acceso a documentos

---

**Última actualización**: 2025-10-25 11:00 UTC
**Versión**: 0.5.0-alpha
**Estado**: Sistema de permisos completado ✅
**Cobertura**: 100% de rutas protegidas

