# Módulo de Gestión Documental - Estructura de Base de Datos

## Resumen General

El módulo de gestión documental cuenta con **12 migraciones** que crean **21 tablas** para gestionar documentos de manera integral.

## Tablas Principales

### 1. Organización Jerárquica

#### `secciones_documentales`
Primer nivel organizativo del sistema documental.
- **Campos clave**: nombre, slug, descripción, icono, color, permisos_defecto
- **Configuración**: requiere_aprobacion, permite_versionado, permite_compartir_externo
- **Características**: Soft deletes, auditoría completa

#### `carpetas_documentales`
Contenedores dentro de las secciones con soporte para jerarquía anidada.
- **Campos clave**: seccion_id, carpeta_padre_id, nombre, ruta_completa, nivel
- **Relaciones CRM**: entidad_tipo, entidad_id (miembro, organización, proyecto, etc.)
- **Estadísticas**: total_documentos, tamano_total_bytes

#### `documentos_gestion`
Almacén principal de documentos con metainformación completa.
- **Información del archivo**: nombre_original, nombre_archivo, ruta, extension, tipo_mime, tamano_bytes, hash_archivo
- **Versionado**: version, documento_original_id, es_version_actual
- **Estado**: borrador, revision, aprobado, archivado, obsoleto
- **Seguridad**: confidencial, nivel_acceso (publico, interno, confidencial, restringido)
- **Búsqueda**: contenido_indexado con fulltext index
- **Estadísticas**: total_descargas, total_visualizaciones, total_compartidos

### 2. Metadatos y Personalización

#### `campos_metadatos`
Define campos personalizados por sección.
- **Tipos soportados**: texto, numero, fecha, desplegable, checkbox, textarea, email, url, telefono
- **Validación**: opciones, requerido, multiple, validacion
- **Búsqueda**: buscable, visible_listado

#### `valores_metadatos`
Almacena valores de metadatos para cada documento.
- **Relación**: documento_id + campo_id = valor

### 3. Control de Versiones

#### `versiones_documentos`
Historial completo de versiones de cada documento.
- **Campos**: numero_version, nombre_archivo, ruta, tamano_bytes, hash_archivo
- **Control**: comentario_version, tipo_cambio (menor, mayor, critico), cambios (JSON)
- **Estado**: activa, descargable

### 4. Compartición y Colaboración

#### `comparticion_documentos`
Gestiona compartición interna y externa de documentos.
- **Tipos**: interno (usuario), externo (email), publico
- **Seguridad**: token único, password_hash, fecha_expiracion
- **Permisos**: puede_ver, puede_descargar, puede_comentar, puede_editar
- **Control de acceso**: max_accesos, accesos_actuales, requiere_autenticacion
- **Auditoría**: ultimo_acceso, ultima_ip

### 5. Sistema de Aprobaciones

#### `flujos_aprobacion`
Define flujos de aprobación configurables.
- **Tipos**: secuencial, paralelo, cualquiera
- **Configuración**: min_aprobadores, requiere_todos, permite_delegar
- **Escalación**: dias_respuesta, escalar_no_respuesta, escalacion_usuarios

#### `aprobadores_flujo`
Aprobadores asignados a cada flujo.
- **Campos**: flujo_id, usuario_id, orden, obligatorio

#### `aprobaciones_documentos`
Solicitudes individuales de aprobación.
- **Estados**: pendiente, aprobado, rechazado, delegado, escalado
- **Delegación**: delegado_a, razon_delegacion
- **Fechas**: fecha_solicitud, fecha_limite, fecha_respuesta, fecha_escalacion
- **Notificaciones**: recordatorios_enviados, ultimo_recordatorio
- **Auditoría**: ip_aprobacion, user_agent

### 6. Firmas Electrónicas

#### `solicitudes_firma`
Gestiona solicitudes de firma electrónica.
- **Tipos**: simple, secuencial, paralelo
- **Estados**: pendiente, en_proceso, completado, rechazado, cancelado
- **Tracking**: total_firmantes, firmantes_completados
- **Resultado**: documento_firmado_ruta, completado_en

#### `firmantes_documento`
Firmantes individuales de cada solicitud.
- **Identificación**: usuario_id (interno) o email+nombre (externo)
- **Acceso externo**: token único
- **Firma**: firma_imagen (base64), firma_tipo, certificado_digital
- **Auditoría completa**: fecha_firma, ip_firma, user_agent, ubicacion_geo, metodo_autenticacion
- **Notificaciones**: recordatorios_enviados, ultimo_recordatorio

### 7. Auditoría Completa

#### `auditoria_documentos`
Registro completo de todas las acciones sobre documentos.
- **Entidades auditadas**: documento, carpeta, seccion, comparticion
- **Acciones**: crear, ver, editar, eliminar, descargar, compartir, mover, aprobar, firmar
- **Usuario**: usuario_id, email_usuario, nombre_usuario
- **Cambios**: datos_anteriores, datos_nuevos (JSON)
- **Sesión**: ip, user_agent, ubicacion_geo, dispositivo, navegador
- **Clasificación**: nivel (info, warning, critical), sospechosa
- **Resultado**: exito, error, bloqueado

### 8. Recordatorios Automáticos

#### `recordatorios_documentos`
Sistema de recordatorios configurables.
- **Tipos**: revision, vencimiento, renovacion, aprobacion, firma, personalizado
- **Destinatarios**: usuarios_ids (JSON), emails_externos (JSON)
- **Frecuencia**: una_vez, diaria, semanal, mensual, anual
- **Anticipación**: dias_anticipacion antes del vencimiento
- **Escalación**: escalar_sin_respuesta, dias_escalacion, usuarios_escalacion
- **Estados**: pendiente, enviado, completado, cancelado
- **Programación**: proximo_envio, max_repeticiones, repeticiones_enviadas

#### `historial_recordatorios`
Tracking de envíos de recordatorios.
- **Campos**: destinatario_email, estado (enviado, entregado, rebotado, error)
- **Tracking**: fecha_envio, fecha_apertura, fecha_click, ip_apertura

### 9. Sistema de Permisos

#### `roles_documentales`
Roles específicos para el módulo documental.
- **Campos**: nombre, slug, descripcion, permisos (JSON)
- **Nivel de acceso**: 1=básico, 2=medio, 3=alto, 4=admin
- **Permisos incluyen**: ver, crear, editar, eliminar, compartir, aprobar, firmar

#### `permisos_usuarios_documentos`
Asignación granular de permisos.
- **Ámbito**: global, seccion, carpeta, documento específico
- **Asignación**: por rol o permisos_personalizados (JSON)
- **Vigencia**: fecha_inicio, fecha_fin, activo

### 10. Comentarios y Colaboración

#### `comentarios_documentos`
Sistema de comentarios con soporte para hilos.
- **Usuario**: usuario_id (interno) o email_externo+nombre_externo
- **Contenido**: contenido, menciones (JSON), archivos_adjuntos (JSON)
- **Posición**: pagina, coordenadas (JSON) para comentarios contextuales
- **Estado**: resuelto, resuelto_por, resuelto_en
- **Estadísticas**: total_respuestas, total_likes

#### `likes_comentarios`
Likes en comentarios.
- **Campos**: comentario_id, usuario_id

## Índices y Optimización

Todas las tablas cuentan con índices optimizados para:
- **Búsquedas frecuentes**: por ID, estado, fechas, usuarios
- **Relaciones**: Foreign keys indexadas
- **Búsqueda fulltext**: En documentos_gestion (titulo, descripcion, contenido_indexado)
- **Composite indexes**: Para consultas complejas frecuentes

## Características de Seguridad

1. **Soft Deletes**: En tablas críticas (documentos, secciones, carpetas)
2. **Auditoría completa**: Creado_por, actualizado_por, eliminado_por
3. **Timestamps**: En todas las tablas
4. **Hash de archivos**: Para detección de duplicados
5. **Tokens únicos**: Para compartición y firmas externas
6. **IP tracking**: En acciones críticas

## Relaciones CRM

- **entidad_tipo** + **entidad_id**: Permite asociar documentos con cualquier entidad del sistema
- Soportado en: `carpetas_documentales`, `documentos_gestion`
- Ejemplos: miembro, organizacion, proyecto, contrato, asamblea, capacitacion

## Escalabilidad

- **Particionamiento**: Tablas diseñadas para soportar millones de registros
- **Caching**: Campos calculados (total_documentos, tamano_total_bytes)
- **Almacenamiento**: Preparado para S3 o almacenamiento local
- **Backup**: Política de retención configurable por sección

## Próximos Pasos

1. ✅ Migraciones creadas y ejecutadas
2. ⏳ Crear modelos Eloquent con relaciones
3. ⏳ Desarrollar controladores
4. ⏳ Crear vistas e interfaces
5. ⏳ Implementar sistema de permisos
6. ⏳ Desarrollar búsqueda avanzada
7. ⏳ Implementar aprobaciones y firmas

