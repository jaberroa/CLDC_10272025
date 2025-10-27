@extends('partials.layouts.master')

@section('title', $carpeta->nombre . ' | CLDCI')
@section('pagetitle', $carpeta->nombre)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Botón Volver -->
        <div class="mb-3">
            <a href="{{ route('gestion-documental.carpetas.index') }}" class="btn btn-soft-secondary">
                <i class="ri-arrow-left-line me-1"></i>
                Volver a Carpetas
            </a>
        </div>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-documental.dashboard') }}">
                        <i class="ri-home-line"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('gestion-documental.carpetas.index') }}">Carpetas</a>
                </li>
                <li class="breadcrumb-item active">{{ $carpeta->nombre }}</li>
            </ol>
        </nav>
        
        <!-- Info de la carpeta -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="font-size: 48px; color: {{ $carpeta->color ?? '#0d6efd' }}">
                            <i class="{{ $carpeta->icono ?? 'ri-folder-line' }}"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h4 class="mb-1">{{ $carpeta->nombre }}</h4>
                        <p class="text-muted mb-0">{{ $carpeta->descripcion }}</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="mostrarModalSubirDocumento()">
                            <i class="ri-upload-cloud-line me-1"></i>
                            Subir Documento
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary-subtle rounded flex-shrink-0">
                                <i class="ri-folder-line fs-3 text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted mb-0">Subcarpetas</p>
                                <h5 class="mb-0">{{ $estadisticas['total_subcarpetas'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-success-subtle rounded flex-shrink-0">
                                <i class="ri-file-line fs-3 text-success"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted mb-0">Documentos</p>
                                <h5 class="mb-0">{{ $estadisticas['total_documentos'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-info-subtle rounded flex-shrink-0">
                                <i class="ri-database-line fs-3 text-info"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted mb-0">Tamaño Total</p>
                                <h5 class="mb-0">{{ number_format($estadisticas['tamano_total'] / 1024 / 1024, 2) }} MB</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Contenido de la Carpeta</h5>
            </div>
            <div class="card-body">
                <!-- Subcarpetas -->
                @if($carpeta->subcarpetas->count() > 0)
                <h6 class="mb-3">
                    <i class="ri-folder-line me-2"></i>
                    Subcarpetas
                </h6>
                <div class="row mb-4">
                    @foreach($carpeta->subcarpetas as $subcarpeta)
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('gestion-documental.carpetas.show', $subcarpeta) }}" 
                           class="card text-decoration-none">
                            <div class="card-body text-center">
                                <i class="{{ $subcarpeta->icono ?? 'ri-folder-line' }} fs-1" 
                                   style="color: {{ $subcarpeta->color ?? '#0d6efd' }}"></i>
                                <h6 class="mt-2 mb-0">{{ $subcarpeta->nombre }}</h6>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <!-- Documentos -->
                @if($carpeta->documentos->count() > 0)
                <h6 class="mb-3">
                    <i class="ri-file-list-line me-2"></i>
                    Documentos
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Tamaño</th>
                                <th>Subido por</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($carpeta->documentos as $documento)
                            <tr>
                                <td>
                                    <i class="ri-file-{{ $documento->extension }}-line me-2"></i>
                                    {{ $documento->titulo }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ strtoupper($documento->extension) }}</span>
                                </td>
                                <td>{{ number_format($documento->tamano_bytes / 1024, 2) }} KB</td>
                                <td>{{ $documento->subidoPor->name ?? 'N/A' }}</td>
                                <td>{{ $documento->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('gestion-documental.documentos.show', $documento) }}" 
                                       class="btn btn-sm btn-soft-primary">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('gestion-documental.documentos.descargar', $documento) }}" 
                                       class="btn btn-sm btn-soft-success">
                                        <i class="ri-download-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
                
                @if($carpeta->subcarpetas->count() == 0 && $carpeta->documentos->count() == 0)
                <div class="text-center py-5">
                    <i class="ri-folder-open-line display-1 text-muted"></i>
                    <h5 class="mt-3">Carpeta vacía</h5>
                    <p class="text-muted">Sube documentos o crea subcarpetas</p>
                    <button class="btn btn-primary mt-2" onclick="mostrarModalSubirDocumento()">
                        <i class="ri-upload-cloud-line me-1"></i>
                        Subir Documento
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir Documento -->
<div class="modal fade" id="modalSubirDocumento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-upload-line me-2"></i>
                    Subir Documentos a: {{ $carpeta->nombre }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSubirDocumento" onsubmit="subirDocumentos(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar archivos</label>
                        <input type="file" class="form-control" id="inputArchivos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif" onchange="mostrarArchivosSeleccionados()">
                        <small class="text-muted">Formatos permitidos: PDF, Word, Excel, PowerPoint, Imágenes</small>
                    </div>
                    
                    <div id="listaArchivosSeleccionados"></div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="ri-information-line me-1"></i>
                        <strong>Carpeta destino:</strong> {{ $carpeta->nombre }}
                        <br>
                        <strong>Sección:</strong> {{ $carpeta->seccion->nombre }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubirDocumentos" disabled>
                        <i class="ri-upload-line me-1"></i>
                        Subir Documentos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let archivosSeleccionados = [];
    
    function mostrarModalSubirDocumento() {
        const modal = new bootstrap.Modal(document.getElementById('modalSubirDocumento'));
        modal.show();
    }
    
    function mostrarArchivosSeleccionados() {
        const input = document.getElementById('inputArchivos');
        archivosSeleccionados = Array.from(input.files);
        
        if (archivosSeleccionados.length === 0) {
            document.getElementById('listaArchivosSeleccionados').innerHTML = '';
            document.getElementById('btnSubirDocumentos').disabled = true;
            return;
        }
        
        let html = '<div class="list-group">';
        archivosSeleccionados.forEach((file, index) => {
            const tamano = (file.size / 1024).toFixed(1);
            const extension = file.name.split('.').pop().toLowerCase();
            
            // Iconos según extensión
            const iconos = {
                'pdf': 'ri-file-pdf-line text-danger',
                'doc': 'ri-file-word-line text-primary',
                'docx': 'ri-file-word-line text-primary',
                'xls': 'ri-file-excel-line text-success',
                'xlsx': 'ri-file-excel-line text-success',
                'ppt': 'ri-file-ppt-line text-warning',
                'pptx': 'ri-file-ppt-line text-warning',
                'jpg': 'ri-image-line text-info',
                'jpeg': 'ri-image-line text-info',
                'png': 'ri-image-line text-info',
                'gif': 'ri-image-line text-info'
            };
            
            const icono = iconos[extension] || 'ri-file-line text-secondary';
            
            html += `
                <div class="list-group-item d-flex align-items-center">
                    <i class="${icono} fs-3 me-3"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${file.name}</div>
                        <small class="text-muted">${tamano} KB</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="eliminarArchivoSeleccionado(${index})">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            `;
        });
        html += '</div>';
        
        document.getElementById('listaArchivosSeleccionados').innerHTML = html;
        document.getElementById('btnSubirDocumentos').disabled = false;
    }
    
    function eliminarArchivoSeleccionado(index) {
        archivosSeleccionados.splice(index, 1);
        const input = document.getElementById('inputArchivos');
        const dt = new DataTransfer();
        archivosSeleccionados.forEach(file => dt.items.add(file));
        input.files = dt.files;
        mostrarArchivosSeleccionados();
    }
    
    function subirDocumentos(e) {
        e.preventDefault();
        
        if (archivosSeleccionados.length === 0) {
            showErrorToast('Por favor selecciona al menos un archivo');
            return;
        }
        
        const btnSubir = document.getElementById('btnSubirDocumentos');
        btnSubir.disabled = true;
        btnSubir.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Subiendo...';
        
        let completados = 0;
        let errores = 0;
        
        archivosSeleccionados.forEach((file, index) => {
            const formData = new FormData();
            formData.append('archivo', file);
            formData.append('carpeta_id', '{{ $carpeta->id }}');
            formData.append('seccion_id', '{{ $carpeta->seccion_id }}');
            formData.append('titulo', file.name.replace(/\.[^/.]+$/, "")); // Nombre sin extensión
            
            fetch('{{ route("gestion-documental.explorador.subir-archivo") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || `HTTP ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                completados++;
                if (data.success) {
                    console.log(`✓ Archivo ${file.name} subido exitosamente`);
                } else {
                    errores++;
                    console.error(`✗ Error al subir ${file.name}:`, data.message);
                    showErrorToast(`Error en ${file.name}: ${data.message}`);
                }
                
                // Si terminamos con todos los archivos
                if (completados === archivosSeleccionados.length) {
                    if (errores === 0) {
                        showSuccessToast(`${completados} archivo(s) subido(s) exitosamente`);
                        $('#modalSubirDocumento').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showErrorToast(`${completados - errores} subidos, ${errores} con errores. Revisa la consola.`);
                        btnSubir.disabled = false;
                        btnSubir.innerHTML = '<i class="ri-upload-line me-1"></i> Reintentar';
                    }
                }
            })
            .catch(error => {
                completados++;
                errores++;
                console.error(`✗ Error crítico al subir ${file.name}:`, error.message);
                showErrorToast(`Error: ${error.message}`);
                
                if (completados === archivosSeleccionados.length) {
                    btnSubir.disabled = false;
                    btnSubir.innerHTML = '<i class="ri-upload-line me-1"></i> Reintentar';
                }
            });
        });
    }
</script>
@endsection

