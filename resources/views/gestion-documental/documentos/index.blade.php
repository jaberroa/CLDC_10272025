@extends('partials.layouts.master')

@section('title', 'Mis Documentos | CLDCI')
@section('pagetitle', 'Explorador de Documentos')

@section('css')
<style>
    .documento-card {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .documento-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .documento-preview {
        height: 150px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px 8px 0 0;
        position: relative;
        overflow: hidden;
    }
    
    .documento-preview i {
        font-size: 60px;
        opacity: 0.3;
    }
    
    .documento-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .badge-extension {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .toolbar {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .breadcrumb-nav {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.dashboard') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver al Dashboard
    </a>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-nav">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('gestion-documental.dashboard') }}">
                    <i class="ri-home-line"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('gestion-documental.documentos.index') }}">Documentos</a>
            </li>
            @if(request('carpeta_id'))
            <li class="breadcrumb-item active">Carpeta Actual</li>
            @endif
        </ol>
    </nav>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <div class="d-flex gap-2">
                <a href="{{ route('gestion-documental.documentos.create') }}" class="btn btn-primary">
                    <i class="ri-upload-cloud-line me-1"></i>
                    Subir Documento
                </a>
                <a href="{{ route('gestion-documental.carpetas.create') }}" class="btn btn-success">
                    <i class="ri-folder-add-line me-1"></i>
                    Nueva Carpeta
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="d-flex gap-2 justify-content-lg-end mt-3 mt-lg-0">
                <!-- Filtros -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-soft-secondary active" data-view="grid">
                        <i class="ri-grid-line"></i>
                    </button>
                    <button type="button" class="btn btn-soft-secondary" data-view="list">
                        <i class="ri-list-check"></i>
                    </button>
                </div>
                
                <select class="form-select w-auto" id="ordenarPor">
                    <option value="created_at_desc">Más reciente</option>
                    <option value="created_at_asc">Más antiguo</option>
                    <option value="titulo_asc">Nombre A-Z</option>
                    <option value="titulo_desc">Nombre Z-A</option>
                    <option value="tamano_desc">Más grande</option>
                    <option value="tamano_asc">Más pequeño</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Grid de documentos -->
<div class="row" id="documentosGrid">
    @forelse($documentos as $documento)
    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 documento-item">
        <div class="documento-card card mb-3" onclick="verDocumento({{ $documento->id }})">
            <div class="documento-preview">
                @if(in_array($documento->extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ asset('storage/' . $documento->ruta) }}" alt="{{ $documento->titulo }}">
                @else
                    <i class="ri-file-{{ $documento->extension }}-line text-primary"></i>
                @endif
                <span class="badge badge-extension bg-dark">{{ strtoupper($documento->extension) }}</span>
            </div>
            <div class="card-body p-3">
                <h6 class="card-title mb-1 text-truncate" title="{{ $documento->titulo }}">
                    {{ Str::limit($documento->titulo, 30) }}
                </h6>
                <p class="text-muted small mb-2">
                    <i class="ri-folder-line me-1"></i>
                    {{ $documento->carpeta->nombre ?? 'Sin carpeta' }}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        {{ $documento->tamano_formateado }}
                    </span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-soft-primary" onclick="verDocumento({{ $documento->id }}, event)" title="Ver">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="btn btn-soft-success" onclick="descargarDocumento({{ $documento->id }}, event)" title="Descargar">
                            <i class="ri-download-line"></i>
                        </button>
                        <button class="btn btn-soft-info" onclick="compartirDocumento({{ $documento->id }}, event)" title="Compartir">
                            <i class="ri-share-line"></i>
                        </button>
                    </div>
                </div>
                
                @if($documento->confidencial)
                <div class="mt-2">
                    <span class="badge bg-danger-subtle text-danger">
                        <i class="ri-lock-line"></i> Confidencial
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="ri-file-search-line display-1 text-muted"></i>
            <h5 class="mt-3">No se encontraron documentos</h5>
            <p class="text-muted">Sube tu primer documento para comenzar</p>
            <a href="{{ route('gestion-documental.documentos.create') }}" class="btn btn-primary mt-3">
                <i class="ri-upload-cloud-line me-1"></i>
                Subir Documento
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Paginación -->
@if($documentos->hasPages())
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Mostrando {{ $documentos->firstItem() }} a {{ $documentos->lastItem() }} de {{ $documentos->total() }} documentos
            </div>
            {{ $documentos->links() }}
        </div>
    </div>
</div>
@endif

<!-- Modal Ver Documento -->
<div class="modal fade" id="verDocumentoModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentoTitulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe id="documentoPreview" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="btnDescargar" class="btn btn-success">
                    <i class="ri-download-line me-1"></i>
                    Descargar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function verDocumento(documentoId, event) {
        if (event) event.stopPropagation();
        
        fetch(`/gestion-documental/documentos/${documentoId}`)
            .then(response => response.text())
            .then(html => {
                // Abrir en nueva página
                window.location.href = `/gestion-documental/documentos/${documentoId}`;
            });
    }

    function descargarDocumento(documentoId, event) {
        event.stopPropagation();
        window.location.href = `/gestion-documental/documentos/${documentoId}/descargar`;
    }

    function compartirDocumento(documentoId, event) {
        event.stopPropagation();
        window.location.href = `/gestion-documental/documentos/${documentoId}#compartir`;
    }

    // Cambiar vista grid/list
    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (view === 'list') {
                // Cambiar a vista de lista
                document.getElementById('documentosGrid').classList.add('list-view');
            } else {
                document.getElementById('documentosGrid').classList.remove('list-view');
            }
        });
    });

    // Ordenar
    document.getElementById('ordenarPor').addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        const [campo, direccion] = this.value.split('_');
        params.set('order_by', campo);
        params.set('order_dir', direccion);
        window.location.search = params.toString();
    });
</script>
@endsection

