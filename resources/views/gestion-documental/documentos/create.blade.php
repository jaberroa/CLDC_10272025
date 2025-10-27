@extends('partials.layouts.master')

@section('title', 'Subir Documento | CLDCI')
@section('pagetitle', 'Subir Nuevo Documento')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    .dropzone {
        border: 3px dashed #0d6efd;
        border-radius: 12px;
        background: #f8f9fa;
        padding: 3rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .dropzone:hover {
        background: rgba(13, 110, 253, 0.05);
        border-color: #0a58ca;
    }
    
    .dropzone .dz-message {
        margin: 0;
    }
    
    .dropzone .dz-preview {
        display: inline-block;
        margin: 0.5rem;
    }
</style>
@endsection

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.documentos.index') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver a Documentos
    </a>
</div>

<form action="{{ route('gestion-documental.documentos.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Zona de carga -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-upload-cloud-line me-2"></i>
                        Seleccionar Archivo
                    </h5>
                </div>
                <div class="card-body">
                    <div id="documentDropzone" class="dropzone">
                        <div class="dz-message">
                            <i class="ri-upload-cloud-2-line display-1 text-primary mb-3"></i>
                            <h4>Arrastra tu archivo aquí</h4>
                            <p class="text-muted">o haz clic para seleccionar desde tu computadora</p>
                            <p class="text-muted small">Formatos soportados: PDF, Word, Excel, PowerPoint, Imágenes</p>
                        </div>
                    </div>
                    
                    <div id="archivoSeleccionado" class="mt-3 d-none">
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="ri-file-line fs-4 me-3"></i>
                                <div class="flex-grow-1">
                                    <strong id="nombreArchivo"></strong>
                                    <p class="mb-0 small" id="detallesArchivo"></p>
                                </div>
                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="limpiarArchivo()">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información del documento -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line me-2"></i>
                        Información del Documento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título del Documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   name="titulo" id="titulo" value="{{ old('titulo') }}" required>
                            @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha del Documento</label>
                            <input type="date" class="form-control" name="fecha_documento" value="{{ old('fecha_documento') }}">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" name="estado" required>
                                <option value="borrador" {{ old('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                <option value="revision" {{ old('estado') == 'revision' ? 'selected' : '' }}>En Revisión</option>
                                <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nivel de Acceso <span class="text-danger">*</span></label>
                            <select class="form-select" name="nivel_acceso" required>
                                <option value="publico" {{ old('nivel_acceso') == 'publico' ? 'selected' : '' }}>Público</option>
                                <option value="interno" {{ old('nivel_acceso') == 'interno' ? 'selected' : '' }} selected>Interno</option>
                                <option value="confidencial" {{ old('nivel_acceso') == 'confidencial' ? 'selected' : '' }}>Confidencial</option>
                                <option value="restringido" {{ old('nivel_acceso') == 'restringido' ? 'selected' : '' }}>Restringido</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confidencial" 
                                       id="confidencial" value="1" {{ old('confidencial') ? 'checked' : '' }}>
                                <label class="form-check-label" for="confidencial">
                                    <i class="ri-lock-line text-danger me-1"></i>
                                    Marcar como documento confidencial
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Ubicación -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-folder-line me-2"></i>
                        Ubicación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sección <span class="text-danger">*</span></label>
                        <select class="form-select @error('seccion_id') is-invalid @enderror" 
                                name="seccion_id" id="seccion_id" required onchange="cargarCarpetas()">
                            <option value="">Seleccionar sección...</option>
                            @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ old('seccion_id', $seccionId) == $seccion->id ? 'selected' : '' }}>
                                {{ $seccion->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('seccion_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Carpeta <span class="text-danger">*</span></label>
                        <select class="form-select @error('carpeta_id') is-invalid @enderror" 
                                name="carpeta_id" id="carpeta_id" required>
                            <option value="">Primero selecciona una sección...</option>
                            @foreach($carpetas as $carpeta)
                            <option value="{{ $carpeta->id }}" {{ old('carpeta_id', $carpetaId) == $carpeta->id ? 'selected' : '' }}>
                                {{ $carpeta->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('carpeta_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Opciones adicionales -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-settings-3-line me-2"></i>
                        Opciones Adicionales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" name="fecha_vencimiento" 
                               value="{{ old('fecha_vencimiento') }}" min="{{ date('Y-m-d') }}">
                        <small class="text-muted">Opcional: para documentos con vigencia limitada</small>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2" id="btnSubir" disabled>
                        <i class="ri-upload-line me-1"></i>
                        Subir Documento
                    </button>
                    <a href="{{ route('gestion-documental.documentos.index') }}" class="btn btn-secondary w-100">
                        <i class="ri-close-line me-1"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    
    let archivoSeleccionado = null;
    
    const myDropzone = new Dropzone("#documentDropzone", {
        url: "#",
        autoProcessQueue: false,
        maxFiles: 1,
        acceptedFiles: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif',
        dictDefaultMessage: "Arrastra tu archivo aquí o haz clic para seleccionar",
        init: function() {
            this.on("addedfile", function(file) {
                archivoSeleccionado = file;
                
                // Mostrar información del archivo
                document.getElementById('nombreArchivo').textContent = file.name;
                document.getElementById('detallesArchivo').textContent = 
                    `Tamaño: ${formatBytes(file.size)} • Tipo: ${file.type}`;
                document.getElementById('archivoSeleccionado').classList.remove('d-none');
                
                // Auto-completar título si está vacío
                if (!document.getElementById('titulo').value) {
                    const nombreSinExtension = file.name.replace(/\.[^/.]+$/, "");
                    document.getElementById('titulo').value = nombreSinExtension;
                }
                
                // Habilitar botón de subir
                document.getElementById('btnSubir').disabled = false;
                
                // Limpiar dropzone visual
                this.removeAllFiles(true);
            });
        }
    });
    
    // Envío del formulario
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        if (!archivoSeleccionado) {
            e.preventDefault();
            showErrorToast('Por favor selecciona un archivo');
            return false;
        }
        
        // Agregar archivo al FormData
        const formData = new FormData(this);
        formData.append('archivo', archivoSeleccionado);
        
        // Deshabilitar botón
        document.getElementById('btnSubir').disabled = true;
        document.getElementById('btnSubir').innerHTML = '<i class="ri-loader-4-line me-1 spinner-border spinner-border-sm"></i> Subiendo...';
    });
    
    function limpiarArchivo() {
        archivoSeleccionado = null;
        document.getElementById('archivoSeleccionado').classList.add('d-none');
        document.getElementById('btnSubir').disabled = true;
        myDropzone.removeAllFiles();
    }
    
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    function cargarCarpetas() {
        const seccionId = document.getElementById('seccion_id').value;
        const carpetaSelect = document.getElementById('carpeta_id');
        
        if (!seccionId) {
            carpetaSelect.innerHTML = '<option value="">Primero selecciona una sección...</option>';
            return;
        }
        
        carpetaSelect.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`/gestion-documental/carpetas/arbol/json?seccion_id=${seccionId}`)
            .then(response => response.json())
            .then(carpetas => {
                carpetaSelect.innerHTML = '<option value="">Seleccionar carpeta...</option>';
                carpetas.forEach(carpeta => {
                    const option = document.createElement('option');
                    option.value = carpeta.id;
                    option.textContent = carpeta.nombre;
                    carpetaSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                carpetaSelect.innerHTML = '<option value="">Error al cargar carpetas</option>';
            });
    }
    
    // Cargar carpetas si hay sección pre-seleccionada
    @if(old('seccion_id', $seccionId))
    cargarCarpetas();
    @endif
</script>
@endsection

