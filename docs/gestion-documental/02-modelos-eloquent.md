# Módulo de Gestión Documental - Modelos Eloquent

## ✅ FASE 2 COMPLETADA: Modelos Eloquent

### Resumen

Se han creado **18 modelos Eloquent** con todas sus relaciones, métodos útiles, scopes y accessors.

## Modelos Creados

### 1. Organización Jerárquica

#### `SeccionDocumental`
- **Relaciones**: carpetas, documentos, camposMetadatos, flujosAprobacion, creadoPor, actualizadoPor
- **Scopes**: activas(), visiblesMenu(), ordenadas()
- **Métodos**: puedeSubirFormato(), puedeSubirTamano()
- **Accessors**: totalCarpetas, totalDocumentos

#### `CarpetaDocumental`
- **Relaciones**: seccion, carpetaPadre, subcarpetas, documentos, entidad (polimórfica)
- **Scopes**: activas(), publicas(), raiz(), porSeccion(), ordenadas()
- **Métodos**: actualizarRutaCompleta(), actualizarEstadisticas(), obtenerArbolCompleto(), moverA()
- **Accessors**: tamanoTotalMb, rutaCompletaArray

#### `DocumentoGestion` 
**Modelo principal más completo**
- **Relaciones**: seccion, carpeta, documentoOriginal, versiones, metadatos, comparticiones, aprobaciones, solicitudesFirma, recordatorios, comentarios, auditoria, entidad (polimórfica)
- **Scopes**: activos(), aprobados(), versionActual(), confidenciales(), porVencer(), buscar()
- **Métodos**: 
  - incrementarDescargas()
  - incrementarVisualizaciones()
  - crearVersion()
  - aprobar()
  - duplicar()
- **Accessors**: tamanoMb, tamanoHumano, url, urlPreview, esImagen, esPdf, esDocumentoOficina, estaVencido, diasParaVencer

### 2. Metadatos Personalizados

#### `CampoMetadato`
- Tipos soportados: texto, numero, fecha, desplegable, checkbox, textarea, email, url, telefono
- **Scopes**: activos(), ordenados(), buscables()

#### `ValorMetadato`
- Almacena valores de metadatos para documentos

### 3. Control de Versiones

#### `VersionDocumento`
- **Método**: activar() - Reactiva una versión anterior
- **Accessors**: url, tamanoMb

### 4. Compartición

#### `ComparticionDocumento`
- Auto-genera tokens únicos
- **Scopes**: activas(), externas()
- **Métodos**: registrarAcceso()
- **Accessors**: estaVigente, urlCompartida

### 5. Aprobaciones

#### `FlujoAprobacion`
- Define flujos configurables (secuencial, paralelo, cualquiera)

#### `AprobadorFlujo`
- Aprobadores asignados a cada flujo

#### `AprobacionDocumento`
- **Scopes**: pendientes()
- **Métodos**: aprobar(), rechazar()

### 6. Firmas Electrónicas

#### `SolicitudFirma`
- **Scopes**: pendientes()
- **Métodos**: verificarCompletado()

#### `FirmanteDocumento`
- Auto-genera tokens únicos
- **Métodos**: firmar()
- **Accessors**: urlFirma

### 7. Auditoría

#### `AuditoriaDocumento`
- **Método estático**: registrar() - Facilita el registro de auditoría
- Captura automática de IP, user agent, timestamps

### 8. Recordatorios

#### `RecordatorioDocumento`
- Soporta frecuencias: una_vez, diaria, semanal, mensual, anual
- Sistema de escalación automática
- **Scopes**: pendientes()

#### `HistorialRecordatorio`
- Tracking de envíos con apertura y clicks

### 9. Permisos

#### `RolDocumental`
- **Métodos**: tienePermiso()
- Roles de sistema no eliminables

#### `PermisoUsuarioDocumento`
- Permisos granulares por ámbito: global, seccion, carpeta, documento
- **Scopes**: vigentes()

### 10. Colaboración

#### `ComentarioDocumento`
- Soporte para hilos de comentarios
- Comentarios contextuales con coordenadas
- **Scopes**: noResueltos()

#### `LikeComentario`
- Sistema de likes en comentarios

## Características Implementadas

### 🔗 Relaciones
- **1:N**: Todas las relaciones jerárquicas
- **N:M**: A través de tablas pivote
- **Polimórficas**: Para entidades CRM (entidad_tipo, entidad_id)
- **Anidadas**: Carpetas con subcarpetas ilimitadas

### 🔍 Scopes
- Filtros comunes pre-configurados
- Reutilizables en controladores
- Optimizados con índices

### ✨ Accessors
- Formateo automático de datos
- Cálculos derivados
- URLs públicas

### 🛠️ Métodos Útiles
- Lógica de negocio encapsulada
- Operaciones complejas simplificadas
- Transacciones seguras

### 🎯 Casts
- Conversión automática de tipos
- Arrays y JSON manejados automáticamente
- Fechas como Carbon instances

## Uso de los Modelos

### Ejemplo 1: Crear Sección

```php
$seccion = SeccionDocumental::create([
    'nombre' => 'Contratos',
    'descripcion' => 'Documentos de contratos',
    'requiere_aprobacion' => true,
    'permite_versionado' => true,
    'max_tamano_archivo_mb' => 50,
    'formatos_permitidos' => ['pdf', 'doc', 'docx'],
    'creado_por' => auth()->id()
]);
```

### Ejemplo 2: Subir Documento

```php
$documento = DocumentoGestion::create([
    'seccion_id' => $seccion->id,
    'carpeta_id' => $carpeta->id,
    'titulo' => 'Contrato de Servicio',
    'nombre_original' => $archivo->getClientOriginalName(),
    'nombre_archivo' => $nombreUnico,
    'ruta' => $ruta,
    'extension' => $archivo->getClientOriginalExtension(),
    'tipo_mime' => $archivo->getMimeType(),
    'tamano_bytes' => $archivo->getSize(),
    'hash_archivo' => hash_file('sha256', $archivo->getRealPath()),
    'estado' => 'borrador',
    'subido_por' => auth()->id()
]);
```

### Ejemplo 3: Compartir Documento

```php
$comparticion = ComparticionDocumento::create([
    'documento_id' => $documento->id,
    'tipo' => 'externo',
    'email_externo' => 'cliente@ejemplo.com',
    'fecha_expiracion' => now()->addDays(7),
    'puede_ver' => true,
    'puede_descargar' => true,
    'compartido_por' => auth()->id()
]);

$url = $comparticion->urlCompartida;
```

### Ejemplo 4: Crear Versión

```php
$version = $documento->crearVersion(
    $archivoNuevo,
    'Corrección de cláusula 5.2'
);
```

### Ejemplo 5: Solicitar Firma

```php
$solicitud = SolicitudFirma::create([
    'documento_id' => $documento->id,
    'titulo' => 'Firma de Contrato',
    'tipo' => 'secuencial',
    'total_firmantes' => 2,
    'creado_por' => auth()->id()
]);

$firmante1 = FirmanteDocumento::create([
    'solicitud_id' => $solicitud->id,
    'usuario_id' => $gerente->id,
    'orden' => 1
]);

$firmante2 = FirmanteDocumento::create([
    'solicitud_id' => $solicitud->id,
    'email' => 'cliente@ejemplo.com',
    'nombre' => 'Juan Pérez',
    'orden' => 2
]);
```

### Ejemplo 6: Registrar Auditoría

```php
AuditoriaDocumento::registrar(
    'descargar',
    $documento,
    'Usuario descargó el documento',
    ['version' => $documento->version]
);
```

### Ejemplo 7: Búsqueda

```php
$documentos = DocumentoGestion::activos()
    ->versionActual()
    ->buscar('contrato')
    ->with(['seccion', 'carpeta', 'subidoPor'])
    ->paginate(20);
```

## Validaciones Recomendadas

### En Requests

```php
public function rules()
{
    return [
        'titulo' => 'required|string|max:500',
        'seccion_id' => 'required|exists:secciones_documentales,id',
        'carpeta_id' => 'required|exists:carpetas_documentales,id',
        'archivo' => 'required|file|max:' . ($seccion->max_tamano_archivo_mb * 1024),
        'estado' => 'in:borrador,revision,aprobado,archivado,obsoleto',
        'nivel_acceso' => 'in:publico,interno,confidencial,restringido',
    ];
}
```

## Próximos Pasos

1. ✅ Modelos creados y relacionados
2. ⏳ Crear Controladores RESTful
3. ⏳ Implementar Servicios de negocio
4. ⏳ Desarrollar Rutas y Middleware
5. ⏳ Crear Vistas e Interfaces

## Estadísticas

- **18 modelos** creados
- **~60 relaciones** definidas
- **~40 scopes** implementados
- **~30 métodos útiles** agregados
- **~20 accessors** para formateo
- **100% cobertura** de tablas de BD

---

**Última actualización**: 2025-10-25 08:45 UTC
**Versión**: 0.2.0-alpha
**Estado**: Modelos Eloquent completados ✅

