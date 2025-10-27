@extends('partials.layouts.master')

@section('title', 'Nueva Carpeta | CLDCI')
@section('pagetitle', 'Crear Nueva Carpeta')

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.carpetas.index') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver a Carpetas
    </a>
</div>

<form action="{{ route('gestion-documental.carpetas.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-folder-add-line me-2"></i>
                        Información de la Carpeta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre de la Carpeta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre') }}" required autofocus>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icono (Remix Icon)</label>
                            <input type="text" class="form-control" name="icono" 
                                   value="{{ old('icono', 'ri-folder-line') }}" 
                                   placeholder="ri-folder-line">
                            <small class="text-muted">Ejemplo: ri-folder-line, ri-folder-2-line</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" name="color" 
                                   value="{{ old('color', '#0d6efd') }}">
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="publica" 
                                       id="publica" value="1" {{ old('publica') ? 'checked' : '' }}>
                                <label class="form-check-label" for="publica">
                                    <i class="ri-eye-line me-1"></i>
                                    Carpeta pública (visible para todos)
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="solo_lectura" 
                                       id="solo_lectura" value="1" {{ old('solo_lectura') ? 'checked' : '' }}>
                                <label class="form-check-label" for="solo_lectura">
                                    <i class="ri-lock-line me-1"></i>
                                    Solo lectura (no se puede modificar)
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
                    <h5 class="card-title mb-0">
                        <i class="ri-folder-settings-line me-2"></i>
                        Ubicación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sección <span class="text-danger">*</span></label>
                        <select class="form-select @error('seccion_id') is-invalid @enderror" 
                                name="seccion_id" id="seccion_id" required>
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
                        <label class="form-label">Carpeta Padre (opcional)</label>
                        <select class="form-select" name="carpeta_padre_id" id="carpeta_padre_id">
                            <option value="">Ninguna (carpeta raíz)</option>
                            @if($carpetaPadreId)
                            <option value="{{ $carpetaPadreId }}" selected>Carpeta seleccionada</option>
                            @endif
                        </select>
                        <small class="text-muted">Deja en blanco para crear en el nivel superior</small>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ri-save-line me-1"></i>
                        Crear Carpeta
                    </button>
                    <a href="{{ route('gestion-documental.carpetas.index') }}" class="btn btn-secondary w-100">
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
    // Auto-generar slug al escribir el nombre
    document.querySelector('input[name="nombre"]').addEventListener('input', function(e) {
        // Opcional: auto-generar slug si necesitas
        console.log('Nombre:', e.target.value);
    });
</script>
@endsection

