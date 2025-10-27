@extends('partials.layouts.master')

@section('title', $seccion->nombre . ' | CLDCI')
@section('pagetitle', $seccion->nombre)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Botón Volver -->
        <div class="mb-3">
            <a href="{{ route('gestion-documental.secciones.index') }}" class="btn btn-soft-secondary">
                <i class="ri-arrow-left-line me-1"></i>
                Volver a Secciones
            </a>
        </div>
        
        <!-- Info de la sección -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="font-size: 64px; color: {{ $seccion->color ?? '#0d6efd' }}">
                            <i class="{{ $seccion->icono ?? 'ri-folder-line' }}"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-1">{{ $seccion->nombre }}</h3>
                        <p class="text-muted mb-2">{{ $seccion->descripcion }}</p>
                        <div>
                            @if($seccion->activa)
                            <span class="badge bg-success">Activa</span>
                            @else
                            <span class="badge bg-secondary">Inactiva</span>
                            @endif
                            
                            @if($seccion->requiere_aprobacion)
                            <span class="badge bg-warning">Requiere Aprobación</span>
                            @endif
                            
                            @if($seccion->permite_versionado)
                            <span class="badge bg-info">Versionado</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('gestion-documental.secciones.edit', $seccion) }}" class="btn btn-warning">
                            <i class="ri-edit-line me-1"></i>
                            Editar
                        </a>
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
                                <p class="text-muted mb-0">Carpetas</p>
                                <h5 class="mb-0">{{ $estadisticas['total_carpetas'] }}</h5>
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
        
        <!-- Carpetas -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-folder-line me-2"></i>
                    Carpetas
                </h5>
                <a href="{{ route('gestion-documental.carpetas.create', ['seccion_id' => $seccion->id]) }}" 
                   class="btn btn-success btn-sm">
                    <i class="ri-folder-add-line me-1"></i>
                    Nueva Carpeta
                </a>
            </div>
            <div class="card-body">
                @if($seccion->carpetas->count() > 0)
                <div class="row">
                    @foreach($seccion->carpetas as $carpeta)
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('gestion-documental.carpetas.show', $carpeta) }}" 
                           class="card text-decoration-none h-100">
                            <div class="card-body text-center">
                                <i class="{{ $carpeta->icono ?? 'ri-folder-line' }} fs-1" 
                                   style="color: {{ $carpeta->color ?? '#0d6efd' }}"></i>
                                <h6 class="mt-2 mb-0">{{ $carpeta->nombre }}</h6>
                                <p class="text-muted small mb-0">{{ $carpeta->documentos->count() }} documentos</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ri-folder-open-line display-1 text-muted"></i>
                    <h5 class="mt-3">No hay carpetas en esta sección</h5>
                    <p class="text-muted">Crea la primera carpeta para organizar documentos</p>
                    <a href="{{ route('gestion-documental.carpetas.create', ['seccion_id' => $seccion->id]) }}" 
                       class="btn btn-success mt-2">
                        <i class="ri-folder-add-line me-1"></i>
                        Crear Carpeta
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

