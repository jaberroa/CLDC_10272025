@extends('partials.layouts.master')

@section('title', 'Editar Tipo de Elección | CLDCI')
@section('title-sub', 'Editar Tipo de Elección')
@section('pagetitle', 'Editar Tipo de Elección')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="ri-edit-line me-2"></i>
                    Editar Tipo de Elección
                </h4>
                <a href="{{ route('tipos-elecciones.index') }}" class="btn btn-soft-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>
                    Volver
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('tipos-elecciones.update', $tipoEleccion) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            Nombre del Tipo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre', $tipoEleccion->nombre) }}" 
                               required 
                               placeholder="Ej: Elección de Vocal">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3" 
                                  placeholder="Describe el propósito de este tipo de elección...">{{ old('descripcion', $tipoEleccion->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icono" class="form-label">Icono (ej: ri-team-line)</label>
                                <input type="text" 
                                       class="form-control @error('icono') is-invalid @enderror" 
                                       id="icono" 
                                       name="icono" 
                                       value="{{ old('icono', $tipoEleccion->icono) }}" 
                                       placeholder="Ej: ri-team-line">
                                @error('icono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color (ej: primary, success)</label>
                                <input type="text" 
                                       class="form-control @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', $tipoEleccion->color) }}" 
                                       placeholder="Ej: primary">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="orden" class="form-label">Orden</label>
                        <input type="number" 
                               class="form-control @error('orden') is-invalid @enderror" 
                               id="orden" 
                               name="orden" 
                               value="{{ old('orden', $tipoEleccion->orden) }}" 
                               min="0">
                        @error('orden')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               role="switch" 
                               id="activo" 
                               name="activo" 
                               value="1"
                               {{ old('activo', $tipoEleccion->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('tipos-elecciones.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>
                            Actualizar Tipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <h5 class="alert-heading">
                <i class="ri-information-line me-2"></i>
                Vista Previa del Tipo
            </h5>
            <div class="d-flex align-items-center">
                <span id="previewIcon" class="badge bg-{{ $tipoEleccion->color ?? 'secondary' }}-subtle text-{{ $tipoEleccion->color ?? 'secondary' }} me-2" style="font-size: 1.2rem; padding: 0.5rem;">
                    <i class="{{ $tipoEleccion->icono ?? 'ri-question-line' }}"></i>
                </span>
                <div>
                    <h6 class="mb-0" id="previewNombre">{{ $tipoEleccion->nombre }}</h6>
                    <small class="text-muted" id="previewDescripcion">{{ $tipoEleccion->descripcion ?? 'Sin descripción' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function updatePreview() {
        const nombre = document.getElementById('nombre').value || 'Nombre del Tipo';
        const descripcion = document.getElementById('descripcion').value || 'Sin descripción';
        const icono = document.getElementById('icono').value || 'ri-question-line';
        const color = document.getElementById('color').value || 'secondary';

        document.getElementById('previewNombre').textContent = nombre;
        document.getElementById('previewDescripcion').textContent = descripcion;
        
        const previewIconSpan = document.getElementById('previewIcon');
        previewIconSpan.innerHTML = `<i class="${icono}"></i>`;
        previewIconSpan.className = `badge bg-${color}-subtle text-${color} me-2`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePreview(); // Initial update
        document.getElementById('nombre').addEventListener('input', updatePreview);
        document.getElementById('descripcion').addEventListener('input', updatePreview);
        document.getElementById('icono').addEventListener('input', updatePreview);
        document.getElementById('color').addEventListener('input', updatePreview);
    });
</script>
@endsection
