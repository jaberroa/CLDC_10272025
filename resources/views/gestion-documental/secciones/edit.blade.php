@extends('partials.layouts.master')

@section('title', 'Editar Sección | CLDCI')
@section('pagetitle', 'Editar Sección: ' . $seccion->nombre)

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.secciones.show', $seccion) }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver a {{ $seccion->nombre }}
    </a>
</div>

<form action="{{ route('gestion-documental.secciones.update', $seccion) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-folders-line me-2"></i>
                        Información de la Sección
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre', $seccion->nombre) }}" required autofocus>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   name="slug" id="slug" value="{{ old('slug', $seccion->slug) }}" required>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $seccion->descripcion) }}</textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Icono (Remix Icon)</label>
                            <input type="text" class="form-control" name="icono" 
                                   value="{{ old('icono', $seccion->icono) }}" 
                                   placeholder="ri-folder-line">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" name="color" 
                                   value="{{ old('color', $seccion->color ?? '#0d6efd') }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" class="form-control" name="orden" 
                                   value="{{ old('orden', $seccion->orden) }}" min="0">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-settings-3-line me-2"></i>
                        Configuración
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tamaño Máximo de Archivo (MB)</label>
                            <input type="number" class="form-control" name="max_tamano_archivo_mb" 
                                   value="{{ old('max_tamano_archivo_mb', $seccion->max_tamano_archivo_mb ?? 50) }}" min="1" max="500">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activa" 
                                       id="activa" value="1" {{ old('activa', $seccion->activa) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">
                                    Sección Activa
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Formatos Permitidos</label>
                            @php
                                $formatosActuales = is_array($seccion->formatos_permitidos) 
                                    ? $seccion->formatos_permitidos 
                                    : json_decode($seccion->formatos_permitidos ?? '[]', true);
                            @endphp
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="formatos_permitidos[]" 
                                               value="pdf" id="pdf" {{ in_array('pdf', $formatosActuales) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pdf">PDF</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="formatos_permitidos[]" 
                                               value="doc" id="doc" {{ in_array('doc', $formatosActuales) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="doc">Word (DOC/DOCX)</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="formatos_permitidos[]" 
                                               value="xls" id="xls" {{ in_array('xls', $formatosActuales) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="xls">Excel (XLS/XLSX)</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="formatos_permitidos[]" 
                                               value="jpg" id="jpg" {{ in_array('jpg', $formatosActuales) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jpg">Imágenes (JPG/PNG)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="visible_menu" 
                                       id="visible_menu" value="1" {{ old('visible_menu', $seccion->visible_menu) ? 'checked' : '' }}>
                                <label class="form-check-label" for="visible_menu">
                                    Visible en menú
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="requiere_aprobacion" 
                                       id="requiere_aprobacion" value="1" {{ old('requiere_aprobacion', $seccion->requiere_aprobacion) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiere_aprobacion">
                                    Requiere aprobación
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permite_versionado" 
                                       id="permite_versionado" value="1" {{ old('permite_versionado', $seccion->permite_versionado) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permite_versionado">
                                    Permite versionado
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permite_compartir_externo" 
                                       id="permite_compartir_externo" value="1" {{ old('permite_compartir_externo', $seccion->permite_compartir_externo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permite_compartir_externo">
                                    Permite compartir externamente
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vista Previa</h5>
                </div>
                <div class="card-body text-center">
                    <div id="preview-icon" style="font-size: 64px; color: {{ $seccion->color ?? '#0d6efd' }};">
                        <i class="{{ $seccion->icono ?? 'ri-folder-line' }}"></i>
                    </div>
                    <h5 id="preview-nombre" class="mt-3">{{ $seccion->nombre }}</h5>
                    <p id="preview-descripcion" class="text-muted">{{ $seccion->descripcion }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ri-save-line me-1"></i>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('gestion-documental.secciones.index') }}" class="btn btn-secondary w-100">
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
    // Auto-generar slug
    document.querySelector('input[name="nombre"]').addEventListener('input', function(e) {
        const nombre = e.target.value;
        const slug = nombre.toLowerCase()
            .replace(/[áàäâ]/g, 'a')
            .replace(/[éèëê]/g, 'e')
            .replace(/[íìïî]/g, 'i')
            .replace(/[óòöô]/g, 'o')
            .replace(/[úùüû]/g, 'u')
            .replace(/ñ/g, 'n')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        
        document.getElementById('slug').value = slug;
        document.getElementById('preview-nombre').textContent = nombre || 'Nombre de Sección';
    });
    
    // Preview descripción
    document.querySelector('textarea[name="descripcion"]').addEventListener('input', function(e) {
        document.getElementById('preview-descripcion').textContent = e.target.value || 'Descripción de la sección';
    });
    
    // Preview icono
    document.querySelector('input[name="icono"]').addEventListener('input', function(e) {
        const iconoHtml = `<i class="${e.target.value || 'ri-folder-line'}"></i>`;
        document.getElementById('preview-icon').innerHTML = iconoHtml;
    });
    
    // Preview color
    document.querySelector('input[name="color"]').addEventListener('input', function(e) {
        document.getElementById('preview-icon').style.color = e.target.value;
    });
</script>
@endsection

