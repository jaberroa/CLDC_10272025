@extends('partials.layouts.master')

@section('title', 'Editar Carpeta | CLDCI')
@section('pagetitle', 'Editar Carpeta: ' . $carpeta->nombre)

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.carpetas.index') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver a Carpetas
    </a>
</div>

<form action="{{ route('gestion-documental.carpetas.update', $carpeta) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-folder-line me-2"></i>
                        Información de la Carpeta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre de la Carpeta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre', $carpeta->nombre) }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $carpeta->descripcion) }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" name="color" 
                                   value="{{ old('color', $carpeta->color ?? '#FFC107') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icono (Remix Icon)</label>
                            <input type="text" class="form-control" name="icono" 
                                   value="{{ old('icono', $carpeta->icono) }}" 
                                   placeholder="ri-folder-line">
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="publica" 
                                       id="publica" value="1" {{ old('publica', $carpeta->publica) ? 'checked' : '' }}>
                                <label class="form-check-label" for="publica">
                                    <i class="ri-eye-line me-1"></i>
                                    Carpeta pública (visible para todos)
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="solo_lectura" 
                                       id="solo_lectura" value="1" {{ old('solo_lectura', $carpeta->solo_lectura) ? 'checked' : '' }}>
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
                    <h5 class="card-title mb-0">Información</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Sección</label>
                        <div class="fw-semibold">{{ $carpeta->seccion->nombre }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Nivel</label>
                        <div class="fw-semibold">Nivel {{ $carpeta->nivel }}</div>
                    </div>
                    
                    @if($carpeta->carpetaPadre)
                    <div class="mb-3">
                        <label class="text-muted small">Carpeta Padre</label>
                        <div class="fw-semibold">{{ $carpeta->carpetaPadre->nombre }}</div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="text-muted small">Subcarpetas</label>
                        <div class="fw-semibold">{{ $carpeta->subcarpetas()->count() }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Documentos</label>
                        <div class="fw-semibold">{{ $carpeta->documentos()->count() }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Creada por</label>
                        <div class="fw-semibold">{{ $carpeta->creadoPor->name ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <label class="text-muted small">Creada el</label>
                        <div class="fw-semibold">{{ $carpeta->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="ri-save-line me-1"></i>
                        Guardar Cambios
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

