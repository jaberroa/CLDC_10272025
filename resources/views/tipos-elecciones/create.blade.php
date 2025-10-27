@extends('partials.layouts.master')

@section('title', 'Nuevo Tipo de Elección | CLDCI')
@section('title-sub', 'Crear Tipo de Elección')
@section('pagetitle', 'Nuevo Tipo de Elección')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="ri-add-line me-2"></i>
                    Crear Nuevo Tipo de Elección
                </h4>
                <a href="{{ route('tipos-elecciones.index') }}" class="btn btn-soft-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>
                    Volver
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="ri-error-warning-line me-2"></i>Errores de validación:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('tipos-elecciones.store') }}" method="POST" id="formCrearTipo">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            Nombre del Tipo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre') }}" 
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
                                  placeholder="Describe el propósito de este tipo de elección...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icono" class="form-label">Icono</label>
                                <select class="form-select @error('icono') is-invalid @enderror" 
                                        id="icono" 
                                        name="icono">
                                    <option value="ri-checkbox-circle-line" {{ old('icono') == 'ri-checkbox-circle-line' ? 'selected' : '' }}>
                                        📋 Checkbox
                                    </option>
                                    <option value="ri-team-line" {{ old('icono') == 'ri-team-line' ? 'selected' : '' }}>
                                        👥 Team
                                    </option>
                                    <option value="ri-group-line" {{ old('icono') == 'ri-group-line' ? 'selected' : '' }}>
                                        👨‍👩‍👧‍👦 Group
                                    </option>
                                    <option value="ri-star-line" {{ old('icono') == 'ri-star-line' ? 'selected' : '' }}>
                                        ⭐ Star
                                    </option>
                                    <option value="ri-award-line" {{ old('icono') == 'ri-award-line' ? 'selected' : '' }}>
                                        🏆 Award
                                    </option>
                                    <option value="ri-medal-line" {{ old('icono') == 'ri-medal-line' ? 'selected' : '' }}>
                                        🏅 Medal
                                    </option>
                                    <option value="ri-flag-line" {{ old('icono') == 'ri-flag-line' ? 'selected' : '' }}>
                                        🚩 Flag
                                    </option>
                                </select>
                                @error('icono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <select class="form-select @error('color') is-invalid @enderror" 
                                        id="color" 
                                        name="color">
                                    <option value="primary" {{ old('color') == 'primary' ? 'selected' : '' }}>🔵 Azul (Primary)</option>
                                    <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>🟢 Verde (Success)</option>
                                    <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>🟡 Amarillo (Warning)</option>
                                    <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>🔴 Rojo (Danger)</option>
                                    <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>🔵 Cian (Info)</option>
                                    <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>⚪ Gris (Secondary)</option>
                                </select>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" 
                                       class="form-control @error('orden') is-invalid @enderror" 
                                       id="orden" 
                                       name="orden" 
                                       value="{{ old('orden', 0) }}" 
                                       min="0">
                                <small class="text-muted">Orden de visualización en listados</small>
                                @error('orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label d-block">Estado</label>
                                <div class="form-check form-switch" style="padding-top: 8px;">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch" 
                                           id="activo" 
                                           name="activo" 
                                           value="1"
                                           {{ old('activo', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <span id="estadoLabel">Activo</span>
                                    </label>
                                </div>
                                <small class="text-muted">Desactiva si no quieres que esté disponible</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('tipos-elecciones.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>
                            Crear Tipo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vista previa -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-eye-line me-2"></i>
                    Vista Previa
                </h5>
            </div>
            <div class="card-body">
                <div id="preview" class="d-flex align-items-center p-3 border rounded">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <div class="avatar-title rounded-circle" id="preview-icon-container">
                                <i class="ri-checkbox-circle-line" id="preview-icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0" id="preview-nombre">Nombre del tipo</h6>
                        <small class="text-muted" id="preview-descripcion">Descripción del tipo</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Toggle estado label
document.getElementById('activo').addEventListener('change', function() {
    const label = document.getElementById('estadoLabel');
    label.textContent = this.checked ? 'Activo' : 'Inactivo';
});

// Vista previa en tiempo real
const nombre = document.getElementById('nombre');
const descripcion = document.getElementById('descripcion');
const icono = document.getElementById('icono');
const color = document.getElementById('color');

function actualizarPreview() {
    const previewNombre = document.getElementById('preview-nombre');
    const previewDescripcion = document.getElementById('preview-descripcion');
    const previewIcon = document.getElementById('preview-icon');
    const previewIconContainer = document.getElementById('preview-icon-container');
    
    previewNombre.textContent = nombre.value || 'Nombre del tipo';
    previewDescripcion.textContent = descripcion.value || 'Descripción del tipo';
    previewIcon.className = icono.value;
    
    // Remover clases anteriores de color
    previewIconContainer.className = 'avatar-title rounded-circle';
    previewIconContainer.classList.add(`bg-${color.value}-subtle`, `text-${color.value}`);
}

nombre.addEventListener('input', actualizarPreview);
descripcion.addEventListener('input', actualizarPreview);
icono.addEventListener('change', actualizarPreview);
color.addEventListener('change', actualizarPreview);
</script>
@endsection


