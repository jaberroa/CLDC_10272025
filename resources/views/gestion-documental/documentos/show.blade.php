@extends('partials.layouts.master')

@section('title', $documento->titulo . ' | CLDCI')
@section('pagetitle', $documento->titulo)

@section('css')
<style>
    .document-viewer {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 2rem;
        min-height: 600px;
    }
    
    .document-preview {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .document-info-card {
        position: sticky;
        top: 20px;
    }
</style>
@endsection

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.carpetas.show', $documento->carpeta_id) }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver a Carpeta
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Información del Documento -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        @php
                            $iconos = [
                                'pdf' => ['icono' => 'ri-file-pdf-line', 'color' => '#dc3545'],
                                'doc' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                'docx' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                'xls' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                'xlsx' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                'ppt' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                                'pptx' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                            ];
                            $info = $iconos[strtolower($documento->extension)] ?? ['icono' => 'ri-file-line', 'color' => '#6c757d'];
                        @endphp
                        <i class="{{ $info['icono'] }}" style="font-size: 64px; color: {{ $info['color'] }}"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-2">{{ $documento->titulo }}</h3>
                        @if($documento->descripcion)
                            <p class="text-muted mb-3">{{ $documento->descripcion }}</p>
                        @endif
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-secondary">{{ strtoupper($documento->extension) }}</span>
                            <span class="badge bg-info">{{ number_format($documento->tamano_bytes / 1024, 2) }} KB</span>
                            <span class="badge bg-{{ $documento->estado == 'aprobado' ? 'success' : 'warning' }}">
                                {{ ucfirst($documento->estado) }}
                            </span>
                            @if($documento->confidencial)
                                <span class="badge bg-danger">
                                    <i class="ri-lock-line me-1"></i>
                                    Confidencial
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Preview del Documento -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-eye-line me-2"></i>
                    Vista Previa
                </h5>
            </div>
            <div class="card-body">
                <div class="document-viewer">
                    @if(in_array(strtolower($documento->extension), ['jpg', 'jpeg', 'png', 'gif']))
                        <!-- Preview de Imagen -->
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $documento->ruta) }}" 
                                 alt="{{ $documento->titulo }}" 
                                 class="img-fluid rounded"
                                 style="max-height: 600px;">
                        </div>
                    @elseif(strtolower($documento->extension) == 'pdf')
                        <!-- Preview de PDF -->
                        <div class="document-preview">
                            <iframe src="{{ asset('storage/' . $documento->ruta) }}" 
                                    width="100%" 
                                    height="600px"
                                    style="border: none; border-radius: 4px;">
                            </iframe>
                        </div>
                    @else
                        <!-- No hay preview disponible -->
                        <div class="text-center py-5">
                            <i class="{{ $info['icono'] }} display-1 text-muted"></i>
                            <h5 class="mt-3">Vista previa no disponible</h5>
                            <p class="text-muted">Este tipo de archivo no se puede previsualizar en el navegador</p>
                            <a href="{{ route('gestion-documental.documentos.descargar', $documento) }}" 
                               class="btn btn-primary mt-3">
                                <i class="ri-download-line me-1"></i>
                                Descargar para Ver
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Acciones -->
        <div class="card mb-3 document-info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('gestion-documental.documentos.descargar', $documento) }}" 
                   class="btn btn-primary w-100 mb-2">
                    <i class="ri-download-line me-1"></i>
                    Descargar
                </a>
                <button class="btn btn-soft-info w-100 mb-2" onclick="mostrarModalCompartir()">
                    <i class="ri-share-line me-1"></i>
                    Compartir
                </button>
                <a href="{{ route('gestion-documental.documentos.edit', $documento) }}" 
                   class="btn btn-soft-warning w-100 mb-2">
                    <i class="ri-edit-line me-1"></i>
                    Editar
                </a>
                <button class="btn btn-soft-danger w-100" onclick="eliminarDocumento()">
                    <i class="ri-delete-bin-line me-1"></i>
                    Eliminar
                </button>
            </div>
        </div>
        
        <!-- Información -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Información</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Nombre del Archivo</label>
                    <div class="fw-semibold">{{ $documento->nombre_original }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Ubicación</label>
                    <div class="fw-semibold">
                        <i class="ri-folder-line me-1"></i>
                        {{ $documento->carpeta->nombre ?? 'N/A' }}
                    </div>
                    <small class="text-muted">{{ $documento->seccion->nombre ?? 'N/A' }}</small>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Subido por</label>
                    <div class="fw-semibold">{{ $documento->subidoPor->name ?? 'N/A' }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Fecha de Subida</label>
                    <div class="fw-semibold">{{ $documento->created_at->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Última Modificación</label>
                    <div class="fw-semibold">{{ $documento->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Tamaño</label>
                    <div class="fw-semibold">{{ number_format($documento->tamano_bytes / 1024 / 1024, 2) }} MB</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Tipo MIME</label>
                    <div class="fw-semibold small">{{ $documento->tipo_mime }}</div>
                </div>
                
                <div>
                    <label class="text-muted small">Versión</label>
                    <div class="fw-semibold">v{{ $documento->version }}</div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Descargas</label>
                    <div class="fw-semibold">{{ $documento->total_descargas }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small">Visualizaciones</label>
                    <div class="fw-semibold">{{ $documento->total_visualizaciones }}</div>
                </div>
                
                <div>
                    <label class="text-muted small">Compartido</label>
                    <div class="fw-semibold">{{ $documento->total_compartidos }} veces</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Compartir Documento -->
<div class="modal fade" id="modalCompartir" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-share-line me-2"></i>
                    Compartir: {{ $documento->titulo }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCompartir" onsubmit="compartirDocumento(event)">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Tipo de Compartición</label>
                            <select class="form-select" id="tipo_comparticion" onchange="cambiarTipoComparticion()">
                                <option value="interno">Compartir con usuarios internos</option>
                                <option value="enlace">Generar enlace público</option>
                                <option value="email">Enviar por email</option>
                            </select>
                        </div>
                        
                        <!-- Compartición Interna -->
                        <div id="seccionInterna" class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Buscar Usuario</label>
                                <input type="text" class="form-control" id="buscarUsuario" 
                                       placeholder="Escribe nombre o email...">
                                <div id="resultadosUsuarios" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Permisos</label>
                                <select class="form-select" id="permiso">
                                    <option value="ver">Solo ver</option>
                                    <option value="descargar">Ver y descargar</option>
                                    <option value="editar">Ver, descargar y editar</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Enlace Público -->
                        <div id="seccionEnlace" class="col-12" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Contraseña (opcional)</label>
                                <input type="password" class="form-control" id="password_enlace" 
                                       placeholder="Dejar vacío para sin contraseña">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Expiración</label>
                                    <input type="date" class="form-control" id="fecha_expiracion">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Máximo de Accesos</label>
                                    <input type="number" class="form-control" id="max_accesos" 
                                           placeholder="Dejar vacío para ilimitado">
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="ri-information-line me-1"></i>
                                El enlace se generará después de hacer clic en "Compartir"
                            </div>
                        </div>
                        
                        <!-- Enviar por Email -->
                        <div id="seccionEmail" class="col-12" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Email del Destinatario</label>
                                <input type="email" class="form-control" id="email_destinatario" 
                                       placeholder="nombre@ejemplo.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mensaje (opcional)</label>
                                <textarea class="form-control" id="mensaje_email" rows="3" 
                                          placeholder="Mensaje personalizado..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notificar" checked>
                                <label class="form-check-label" for="notificar">
                                    Notificar a los destinatarios
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-share-line me-1"></i>
                        Compartir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Enlace Generado -->
<div class="modal fade" id="modalEnlaceGenerado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="ri-check-line me-2"></i>
                    Enlace Generado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">El enlace de compartición ha sido generado exitosamente:</p>
                
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="enlaceGenerado" readonly>
                    <button class="btn btn-primary" onclick="copiarEnlace()">
                        <i class="ri-file-copy-line"></i>
                    </button>
                </div>
                
                <div class="alert alert-warning small">
                    <i class="ri-alert-line me-1"></i>
                    Guarda este enlace. Cualquiera con acceso podrá ver el documento.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let usuarioSeleccionado = null;
    
    // Incrementar contador de visualizaciones
    fetch('{{ route("gestion-documental.documentos.show", $documento) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ incrementar_visualizacion: true })
    });
    
    function mostrarModalCompartir() {
        $('#modalCompartir').modal('show');
    }
    
    function cambiarTipoComparticion() {
        const tipo = document.getElementById('tipo_comparticion').value;
        
        document.getElementById('seccionInterna').style.display = tipo === 'interno' ? 'block' : 'none';
        document.getElementById('seccionEnlace').style.display = tipo === 'enlace' ? 'block' : 'none';
        document.getElementById('seccionEmail').style.display = tipo === 'email' ? 'block' : 'none';
    }
    
    // Búsqueda de usuarios en tiempo real
    let timeoutBusqueda;
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscarUsuario');
        if (inputBuscar) {
            inputBuscar.addEventListener('input', function() {
                clearTimeout(timeoutBusqueda);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    document.getElementById('resultadosUsuarios').innerHTML = '';
                    return;
                }
                
                timeoutBusqueda = setTimeout(() => buscarUsuarios(query), 300);
            });
        }
    });
    
    function buscarUsuarios(query) {
        fetch(`/api/usuarios/buscar?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(usuarios => {
                const container = document.getElementById('resultadosUsuarios');
                container.innerHTML = '';
                
                if (usuarios.length === 0) {
                    container.innerHTML = '<div class="list-group-item text-muted">No se encontraron usuarios</div>';
                    return;
                }
                
                usuarios.forEach(usuario => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs flex-shrink-0 me-3">
                                <span class="avatar-title rounded-circle bg-primary">${usuario.name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">${usuario.name}</h6>
                                <small class="text-muted">${usuario.email}</small>
                            </div>
                        </div>
                    `;
                    item.onclick = () => seleccionarUsuario(usuario);
                    container.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Error al buscar usuarios:', error);
            });
    }
    
    function seleccionarUsuario(usuario) {
        usuarioSeleccionado = usuario;
        document.getElementById('buscarUsuario').value = usuario.name;
        document.getElementById('resultadosUsuarios').innerHTML = `
            <div class="list-group-item active">
                <i class="ri-check-line me-2"></i>
                Seleccionado: ${usuario.name} (${usuario.email})
            </div>
        `;
    }
    
    function compartirDocumento(e) {
        e.preventDefault();
        
        const tipo = document.getElementById('tipo_comparticion').value;
        const notificar = document.getElementById('notificar').checked;
        
        let data = {
            documento_id: {{ $documento->id }},
            tipo: tipo,
            notificar: notificar
        };
        
        // Validar según tipo
        if (tipo === 'interno') {
            if (!usuarioSeleccionado) {
                showErrorToast('Por favor selecciona un usuario');
                return;
            }
            data.usuario_id = usuarioSeleccionado.id;
            data.permiso = document.getElementById('permiso').value;
        } else if (tipo === 'enlace') {
            data.password = document.getElementById('password_enlace').value;
            data.fecha_expiracion = document.getElementById('fecha_expiracion').value;
            data.max_accesos = document.getElementById('max_accesos').value;
        } else if (tipo === 'email') {
            const email = document.getElementById('email_destinatario').value;
            if (!email) {
                showErrorToast('Por favor ingresa un email');
                return;
            }
            data.email = email;
            data.mensaje = document.getElementById('mensaje_email').value;
        }
        
        // Enviar solicitud
        fetch('{{ route("gestion-documental.comparticion.compartir", $documento) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message || 'Documento compartido exitosamente');
                $('#modalCompartir').modal('hide');
                
                // Si es enlace público, mostrar el enlace
                if (tipo === 'enlace' && data.enlace) {
                    document.getElementById('enlaceGenerado').value = data.enlace;
                    $('#modalEnlaceGenerado').modal('show');
                }
                
                // Limpiar formulario
                document.getElementById('formCompartir').reset();
                usuarioSeleccionado = null;
                document.getElementById('resultadosUsuarios').innerHTML = '';
            } else {
                showErrorToast(data.message || 'Error al compartir el documento');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al compartir el documento');
        });
    }
    
    function copiarEnlace() {
        const input = document.getElementById('enlaceGenerado');
        input.select();
        document.execCommand('copy');
        showSuccessToast('Enlace copiado al portapapeles');
    }
    
    function eliminarDocumento() {
        if (!confirm('¿Estás seguro de eliminar este documento? Esta acción no se puede deshacer.')) {
            return;
        }
        
        fetch('{{ route("gestion-documental.documentos.destroy", $documento) }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Documento eliminado exitosamente');
                setTimeout(() => {
                    window.location = '{{ route("gestion-documental.carpetas.show", $documento->carpeta_id) }}';
                }, 1000);
            } else {
                showErrorToast(data.message || 'Error al eliminar el documento');
            }
        })
        .catch(error => {
            showErrorToast('Error al eliminar el documento');
        });
    }
</script>
@endsection

