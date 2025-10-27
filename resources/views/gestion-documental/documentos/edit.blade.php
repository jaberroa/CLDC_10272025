@extends('partials.layouts.master')

@section('title', 'Editar Documento | CLDCI')
@section('pagetitle', 'Editar: ' . $documento->titulo)

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.documentos.show', $documento) }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver al Documento
    </a>
</div>

<form action="{{ route('gestion-documental.documentos.update', $documento) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Información Principal -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line me-2"></i>
                        Información del Documento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   name="titulo" value="{{ old('titulo', $documento->titulo) }}" required>
                            @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="4">{{ old('descripcion', $documento->descripcion) }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="borrador" {{ old('estado', $documento->estado) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                <option value="revision" {{ old('estado', $documento->estado) == 'revision' ? 'selected' : '' }}>En Revisión</option>
                                <option value="aprobado" {{ old('estado', $documento->estado) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                <option value="archivado" {{ old('estado', $documento->estado) == 'archivado' ? 'selected' : '' }}>Archivado</option>
                                <option value="obsoleto" {{ old('estado', $documento->estado) == 'obsoleto' ? 'selected' : '' }}>Obsoleto</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nivel de Acceso</label>
                            <select class="form-select" name="nivel_acceso">
                                <option value="publico" {{ old('nivel_acceso', $documento->nivel_acceso) == 'publico' ? 'selected' : '' }}>Público</option>
                                <option value="interno" {{ old('nivel_acceso', $documento->nivel_acceso) == 'interno' ? 'selected' : '' }}>Interno</option>
                                <option value="confidencial" {{ old('nivel_acceso', $documento->nivel_acceso) == 'confidencial' ? 'selected' : '' }}>Confidencial</option>
                                <option value="restringido" {{ old('nivel_acceso', $documento->nivel_acceso) == 'restringido' ? 'selected' : '' }}>Restringido</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confidencial" 
                                       id="confidencial" value="1" {{ old('confidencial', $documento->confidencial) ? 'checked' : '' }}>
                                <label class="form-check-label" for="confidencial">
                                    <i class="ri-lock-line text-danger me-1"></i>
                                    Marcar como documento confidencial
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fechas Importantes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-calendar-line me-2"></i>
                        Fechas Importantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha del Documento</label>
                            <input type="date" class="form-control" name="fecha_documento" 
                                   value="{{ old('fecha_documento', $documento->fecha_documento ? $documento->fecha_documento->format('Y-m-d') : '') }}">
                            <small class="text-muted">Fecha original del documento</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" name="fecha_vencimiento" 
                                   value="{{ old('fecha_vencimiento', $documento->fecha_vencimiento ? $documento->fecha_vencimiento->format('Y-m-d') : '') }}">
                            <small class="text-muted">Para documentos con vigencia</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Revisión</label>
                            <input type="date" class="form-control" name="fecha_revision" 
                                   value="{{ old('fecha_revision', $documento->fecha_revision ? $documento->fecha_revision->format('Y-m-d') : '') }}">
                            <small class="text-muted">Próxima fecha de revisión</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Ubicación -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubicación</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sección</label>
                        <select class="form-select" name="seccion_id" id="seccion_id" onchange="cargarCarpetas()">
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}" {{ old('seccion_id', $documento->seccion_id) == $seccion->id ? 'selected' : '' }}>
                                    {{ $seccion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Carpeta</label>
                        <select class="form-select" name="carpeta_id" id="carpeta_id">
                            @foreach($carpetas as $carpeta)
                                <option value="{{ $carpeta->id }}" {{ old('carpeta_id', $documento->carpeta_id) == $carpeta->id ? 'selected' : '' }}>
                                    {{ $carpeta->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Información del Archivo -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Archivo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nombre Original</label>
                        <div class="fw-semibold small">{{ $documento->nombre_original }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Tipo</label>
                        <div>
                            <span class="badge bg-secondary">{{ strtoupper($documento->extension) }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Tamaño</label>
                        <div class="fw-semibold">{{ number_format($documento->tamano_bytes / 1024, 2) }} KB</div>
                    </div>
                    
                    <div>
                        <label class="text-muted small">Versión</label>
                        <div class="fw-semibold">v{{ $documento->version }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ri-save-line me-1"></i>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('gestion-documental.documentos.show', $documento) }}" 
                       class="btn btn-secondary w-100">
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
<script>
    function cargarCarpetas() {
        const seccionId = document.getElementById('seccion_id').value;
        const carpetaSelect = document.getElementById('carpeta_id');
        
        if (!seccionId) return;
        
        carpetaSelect.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`/gestion-documental/carpetas/arbol/json?seccion_id=${seccionId}`)
            .then(response => response.json())
            .then(carpetas => {
                carpetaSelect.innerHTML = '<option value="">Seleccionar carpeta...</option>';
                carpetas.forEach(carpeta => {
                    const option = document.createElement('option');
                    option.value = carpeta.id;
                    option.textContent = carpeta.nombre;
                    if (carpeta.id == {{ $documento->carpeta_id }}) {
                        option.selected = true;
                    }
                    carpetaSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                carpetaSelect.innerHTML = '<option value="">Error al cargar carpetas</option>';
            });
    }
</script>
@endsection

