@extends('partials.layouts.master')

@section('title', 'Búsqueda de Documentos | CLDCI')
@section('pagetitle', 'Búsqueda Avanzada')

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.dashboard') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver al Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('gestion-documental.busqueda.index') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" class="form-control" name="q" 
                                       value="{{ request('q') }}" 
                                       placeholder="Buscar documentos..." autofocus>
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-soft-secondary" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados">
                                <i class="ri-filter-line me-1"></i>
                                Filtros Avanzados
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse mt-3" id="filtrosAvanzados">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Sección</label>
                                <select class="form-select" name="seccion_id">
                                    <option value="">Todas</option>
                                    @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                        {{ $seccion->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Formato</label>
                                <select class="form-select" name="extension">
                                    <option value="">Todos</option>
                                    @foreach($extensiones as $ext)
                                    <option value="{{ $ext }}" {{ request('extension') == $ext ? 'selected' : '' }}>
                                        {{ strtoupper($ext) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Desde</label>
                                <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hasta</label>
                                <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @if(request('q') || request()->hasAny(['seccion_id', 'extension', 'fecha_desde', 'fecha_hasta']))
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Resultados de búsqueda ({{ $documentos->total() }})
                </h5>
            </div>
            <div class="card-body">
                @forelse($documentos as $documento)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-primary text-primary rounded">
                                <i class="ri-file-{{ $documento->extension }}-line fs-4"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">
                                <a href="{{ route('gestion-documental.documentos.show', $documento) }}">
                                    {{ $documento->titulo }}
                                </a>
                            </h6>
                            <p class="text-muted mb-2">{{ Str::limit($documento->descripcion, 150) }}</p>
                            <p class="text-muted small mb-0">
                                <i class="ri-folder-line me-1"></i> {{ $documento->seccion->nombre }} / {{ $documento->carpeta->nombre }}
                                <span class="mx-2">•</span>
                                <i class="ri-calendar-line me-1"></i> {{ $documento->created_at->format('d/m/Y') }}
                                <span class="mx-2">•</span>
                                {{ $documento->tamano_formateado }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="ri-file-search-line display-1 text-muted"></i>
                    <h5 class="mt-3">No se encontraron resultados</h5>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                </div>
                @endforelse
                
                @if($documentos->hasPages())
                <div class="mt-3">
                    {{ $documentos->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

