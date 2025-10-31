@props([
    'tiposOrganizacion' => [],
    'organizacion' => null,
    'method' => 'POST',
    'action' => null,
    'submitLabel' => 'Guardar',
    'submitIcon' => 'ri-save-line',
    'submitClass' => 'btn btn-primary',
    'cancelLabel' => 'Cancelar',
    'cancelIcon' => 'ri-close-line',
    'cancelClass' => 'btn btn-outline-secondary',
    'alignButtons' => 'start'
])

@php
    $isEdit = $organizacion !== null;
    $formAction = $action ?? ($isEdit ? route('organizaciones.update', $organizacion->id) : route('organizaciones.store'));
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="organizacion-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    {{ $extra ?? '' }}

    <!-- Información Básica -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-building-line me-2"></i>
                Información Básica
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                   id="nombre" name="nombre" value="{{ old('nombre', $organizacion->nombre ?? '') }}" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                   id="codigo" name="codigo" value="{{ old('codigo', $organizacion->codigo ?? '') }}" required>
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="tipo" class="form-label">Tipo de Organización <span class="text-danger">*</span></label>
            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                <option value="">Seleccione un tipo</option>
                @foreach($tiposOrganizacion as $tipo)
                    <option value="{{ $tipo->nombre }}" 
                            {{ old('tipo', $organizacion->tipo ?? '') == $tipo->nombre ? 'selected' : '' }}>
                        {{ ucfirst($tipo->nombre) }}
                    </option>
                @endforeach
            </select>
            @error('tipo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                <option value="activa" {{ old('estado', $organizacion->estado ?? '') == 'activa' ? 'selected' : '' }}>Activa</option>
                <option value="inactiva" {{ old('estado', $organizacion->estado ?? '') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                <option value="suspendida" {{ old('estado', $organizacion->estado ?? '') == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
            </select>
            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                      id="descripcion" name="descripcion" rows="3" 
                      placeholder="Descripción de la organización">{{ old('descripcion', $organizacion->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Información de Contacto -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-phone-line me-2"></i>
                Información de Contacto
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $organizacion->email ?? '') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control @error('telefono') is-invalid @enderror" 
                   id="telefono" name="telefono" value="{{ old('telefono', $organizacion->telefono ?? '') }}">
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control @error('direccion') is-invalid @enderror" 
                      id="direccion" name="direccion" rows="2" 
                      placeholder="Dirección completa de la organización">{{ old('direccion', $organizacion->direccion ?? '') }}</textarea>
            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Logo de la Organización -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-image-line me-2"></i>
                Logo de la Organización
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                   id="logo" name="logo" accept="image/*">
            @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-center align-items-center" style="height: 100px; border: 2px dashed #dee2e6; border-radius: 8px;">
                @if($isEdit && $organizacion->logo_url)
                    <img src="{{ $organizacion->logo_url }}" alt="Logo actual" class="img-fluid" style="max-height: 80px;">
                @else
                    <img id="logo-preview" src="" alt="Preview" class="img-fluid" style="max-height: 80px; display: none;">
                    <span class="text-muted" id="logo-placeholder">Preview del logo</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 justify-content-{{ $alignButtons }}">
                <button type="submit" class="{{ $submitClass }}">
                    <i class="{{ $submitIcon }} me-1"></i>
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('organizaciones.index') }}" class="{{ $cancelClass }}">
                    <i class="{{ $cancelIcon }} me-1"></i>
                    {{ $cancelLabel }}
                </a>
            </div>
        </div>
    </div>
</form>