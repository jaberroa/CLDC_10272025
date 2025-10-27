@extends('partials.layouts.master')

@section('title', 'Explorador de Documentos | CLDCI')
@section('title-sub', 'Gestión de Documentos')
@section('pagetitle', 'Mi Unidad')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<style>
    .explorador-toolbar {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .breadcrumb-explorador {
        background: transparent;
        padding: 0.5rem 0;
        margin: 0;
        font-size: 14px;
    }
    
    .breadcrumb-explorador .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 18px;
        color: #6c757d;
    }
    
    .item-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s;
        cursor: pointer;
        height: 100%;
    }
    
    .item-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .item-card.seleccionado {
        background: #e7f3ff;
        border-color: #0d6efd;
    }
    
    .carpeta-icon {
        font-size: 48px;
        color: #FFC107;
    }
    
    .archivo-icon {
        font-size: 48px;
    }
    
    .item-nombre {
        font-weight: 500;
        margin-top: 0.5rem;
        font-size: 14px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-meta {
        font-size: 12px;
        color: #6c757d;
    }
    
    .vista-lista .item-row {
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .vista-lista .item-row:hover {
        background: #f8f9fa;
        border-color: #0d6efd;
    }
    
    .menu-contextual {
        position: absolute;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        min-width: 200px;
        display: none;
    }
    
    .menu-contextual.show {
        display: block;
    }
    
    .menu-contextual-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .menu-contextual-item:hover {
        background: #f8f9fa;
    }
    
    .menu-contextual-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<!-- Estadísticas -->
<div class="row mb-3">
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Total Carpetas"
            :value="number_format($estadisticas['total_carpetas'] ?? 0)"
            icon="ri-folder-line"
            background="bg-primary-subtle"
            icon-background="bg-primary"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Total Archivos"
            :value="number_format($estadisticas['total_archivos'] ?? 0)"
            icon="ri-file-text-line"
            background="bg-success-subtle"
            icon-background="bg-success"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Espacio Usado"
            :value="number_format(($estadisticas['espacio_usado'] ?? 0) / 1024 / 1024, 2) . ' MB'"
            icon="ri-database-line"
            background="bg-info-subtle"
            icon-background="bg-info"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Compartidos"
            :value="number_format($estadisticas['total_compartidos'] ?? 0)"
            icon="ri-share-line"
            background="bg-warning-subtle"
            icon-background="bg-warning"
        />
    </div>
</div>

<!-- Toolbar -->
<div class="explorador-toolbar">
    <div class="row align-items-center">
        <div class="col-md-6">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-explorador mb-0">
                    @foreach($breadcrumb as $item)
                        @if($loop->last)
                            <li class="breadcrumb-item active">{{ $item['nombre'] }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $item['url'] }}">{{ $item['nombre'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        
        <div class="col-md-6 text-end">
            <!-- Acciones -->
            <div class="btn-group me-2">
                <button class="btn btn-sm btn-soft-primary" onclick="mostrarModalNuevaCarpeta()">
                    <i class="ri-folder-add-line me-1"></i>
                    Nueva Carpeta
                </button>
                <button class="btn btn-sm btn-primary" onclick="mostrarModalSubirArchivo()">
                    <i class="ri-upload-line me-1"></i>
                    Subir Archivo
                </button>
            </div>
            
            <!-- Vista -->
            <div class="btn-group">
                <button class="btn btn-sm btn-soft-secondary {{ $vista == 'cuadricula' ? 'active' : '' }}" 
                        onclick="cambiarVista('cuadricula')">
                    <i class="ri-layout-grid-line"></i>
                </button>
                <button class="btn btn-sm btn-soft-secondary {{ $vista == 'lista' ? 'active' : '' }}" 
                        onclick="cambiarVista('lista')">
                    <i class="ri-list-check"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contenido -->
<div class="card">
    <div class="card-body">
        @if($vista == 'cuadricula')
            <!-- Vista Cuadrícula -->
            <div class="row g-3" id="contenedorItems">
                @forelse($carpetas as $carpeta)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="item-card" 
                             data-tipo="carpeta" 
                             data-id="{{ $carpeta->id }}"
                             ondblclick="abrirCarpeta({{ $carpeta->id }})"
                             oncontextmenu="mostrarMenuContextual(event, 'carpeta', {{ $carpeta->id }})">
                            <div class="text-center">
                                <i class="ri-folder-fill carpeta-icon" style="color: {{ $carpeta->color ?? '#FFC107' }}"></i>
                                <div class="item-nombre" title="{{ $carpeta->nombre }}">
                                    {{ $carpeta->nombre }}
                                </div>
                                <div class="item-meta">
                                    {{ $carpeta->documentos->count() }} elementos
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
                
                @forelse($documentos as $documento)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="item-card" 
                             data-tipo="documento" 
                             data-id="{{ $documento->id }}"
                             ondblclick="abrirDocumento({{ $documento->id }})"
                             oncontextmenu="mostrarMenuContextual(event, 'documento', {{ $documento->id }})">
                            <div class="text-center">
                                @php
                                    $iconos = [
                                        'pdf' => ['icono' => 'ri-file-pdf-line', 'color' => '#dc3545'],
                                        'doc' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                        'docx' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                        'xls' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                        'xlsx' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                        'ppt' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                                        'pptx' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                                        'jpg' => ['icono' => 'ri-image-line', 'color' => '#6c757d'],
                                        'jpeg' => ['icono' => 'ri-image-line', 'color' => '#6c757d'],
                                        'png' => ['icono' => 'ri-image-line', 'color' => '#6c757d'],
                                    ];
                                    $ext = strtolower($documento->extension);
                                    $info = $iconos[$ext] ?? ['icono' => 'ri-file-line', 'color' => '#6c757d'];
                                @endphp
                                <i class="{{ $info['icono'] }} archivo-icon" style="color: {{ $info['color'] }}"></i>
                                <div class="item-nombre" title="{{ $documento->titulo }}">
                                    {{ $documento->titulo }}
                                </div>
                                <div class="item-meta">
                                    {{ number_format($documento->tamano_bytes / 1024, 1) }} KB
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
                
                @if($carpetas->isEmpty() && $documentos->isEmpty())
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri-folder-open-line display-1 text-muted"></i>
                            <h5 class="mt-3">Esta carpeta está vacía</h5>
                            <p class="text-muted">Arrastra archivos aquí o usa el botón "Subir Archivo"</p>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Vista Lista -->
            <div class="vista-lista" id="contenedorItems">
                @foreach($carpetas as $carpeta)
                    <div class="item-row d-flex align-items-center" 
                         data-tipo="carpeta" 
                         data-id="{{ $carpeta->id }}"
                         ondblclick="abrirCarpeta({{ $carpeta->id }})"
                         oncontextmenu="mostrarMenuContextual(event, 'carpeta', {{ $carpeta->id }})">
                        <div class="flex-shrink-0">
                            <i class="ri-folder-fill fs-3" style="color: {{ $carpeta->color ?? '#FFC107' }}"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-semibold">{{ $carpeta->nombre }}</div>
                            <small class="text-muted">{{ $carpeta->documentos->count() }} elementos</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $carpeta->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                @endforeach
                
                @foreach($documentos as $documento)
                    <div class="item-row d-flex align-items-center" 
                         data-tipo="documento" 
                         data-id="{{ $documento->id }}"
                         ondblclick="abrirDocumento({{ $documento->id }})"
                         oncontextmenu="mostrarMenuContextual(event, 'documento', {{ $documento->id }})">
                        <div class="flex-shrink-0">
                            @php
                                $iconos = [
                                    'pdf' => ['icono' => 'ri-file-pdf-line', 'color' => '#dc3545'],
                                    'doc' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                    'docx' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                    'xls' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                    'xlsx' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                ];
                                $ext = strtolower($documento->extension);
                                $info = $iconos[$ext] ?? ['icono' => 'ri-file-line', 'color' => '#6c757d'];
                            @endphp
                            <i class="{{ $info['icono'] }} fs-3" style="color: {{ $info['color'] }}"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-semibold">{{ $documento->titulo }}</div>
                            <small class="text-muted">{{ number_format($documento->tamano_bytes / 1024, 1) }} KB</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $documento->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Menú Contextual -->
<div id="menuContextual" class="menu-contextual">
    <div class="menu-contextual-item" onclick="abrirItem()">
        <i class="ri-folder-open-line me-2"></i> Abrir
    </div>
    <div class="menu-contextual-item" onclick="renombrarItem()">
        <i class="ri-edit-line me-2"></i> Renombrar
    </div>
    <div class="menu-contextual-item" onclick="moverItem()">
        <i class="ri-folder-transfer-line me-2"></i> Mover a
    </div>
    <div class="menu-contextual-item" onclick="compartirItem()">
        <i class="ri-share-line me-2"></i> Compartir
    </div>
    <div class="menu-contextual-item text-danger" onclick="eliminarItem()">
        <i class="ri-delete-bin-line me-2"></i> Eliminar
    </div>
</div>

<!-- Modal Nueva Carpeta -->
@include('gestion-documental.explorador.modals.nueva-carpeta')

<!-- Modal Subir Archivo -->
@include('gestion-documental.explorador.modals.subir-archivo')

@endsection

@section('js')
<script>
    let itemSeleccionado = null;
    let tipoSeleccionado = null;
    
    // Cambiar vista
    function cambiarVista(vista) {
        const url = new URL(window.location);
        url.searchParams.set('vista', vista);
        window.location = url;
    }
    
    // Abrir carpeta
    function abrirCarpeta(id) {
        window.location = `{{ route('gestion-documental.explorador.index') }}?carpeta_id=${id}`;
    }
    
    // Abrir documento
    function abrirDocumento(id) {
        window.location = `/gestion-documental/documentos/${id}`;
    }
    
    // Menú contextual
    function mostrarMenuContextual(event, tipo, id) {
        event.preventDefault();
        itemSeleccionado = id;
        tipoSeleccionado = tipo;
        
        const menu = document.getElementById('menuContextual');
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';
        menu.classList.add('show');
        
        return false;
    }
    
    // Cerrar menú contextual al hacer clic fuera
    document.addEventListener('click', function() {
        document.getElementById('menuContextual').classList.remove('show');
    });
    
    // Modales
    function mostrarModalNuevaCarpeta() {
        $('#modalNuevaCarpeta').modal('show');
    }
    
    function mostrarModalSubirArchivo() {
        $('#modalSubirArchivo').modal('show');
    }
    
    // Acciones del menú contextual
    function abrirItem() {
        if (tipoSeleccionado == 'carpeta') {
            abrirCarpeta(itemSeleccionado);
        } else {
            abrirDocumento(itemSeleccionado);
        }
    }
    
    function renombrarItem() {
        const nuevoNombre = prompt('Nuevo nombre:');
        if (!nuevoNombre) return;
        
        fetch('{{ route("gestion-documental.explorador.renombrar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipo: tipoSeleccionado,
                id: itemSeleccionado,
                nuevo_nombre: nuevoNombre
            })
        })
        .then(r => r.json())
        .then(data => {
            showSuccessToast(data.message);
            location.reload();
        });
    }
    
    function eliminarItem() {
        if (!confirm('¿Estás seguro de eliminar este elemento?')) return;
        
        fetch('{{ route("gestion-documental.explorador.eliminar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tipo: tipoSeleccionado,
                id: itemSeleccionado
            })
        })
        .then(r => r.json())
        .then(data => {
            showSuccessToast(data.message);
            location.reload();
        });
    }
    
    // Drag and drop
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('contenedorItems');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-primary', 'bg-light');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-primary', 'bg-light');
            }, false);
        });
        
        dropZone.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                // Subir archivos
                Array.from(files).forEach(file => {
                    subirArchivoDragDrop(file);
                });
            }
        }, false);
    });
    
    function subirArchivoDragDrop(file) {
        const formData = new FormData();
        formData.append('archivo', file);
        formData.append('carpeta_id', '{{ request()->carpeta_id ?? "" }}');
        formData.append('seccion_id', '{{ $carpetaActual->seccion_id ?? request()->seccion_id ?? 1 }}');
        
        fetch('{{ route("gestion-documental.explorador.subir-archivo") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showSuccessToast(`${file.name} subido exitosamente`);
            location.reload();
        })
        .catch(error => {
            showErrorToast('Error al subir el archivo');
        });
    }
</script>
@endsection

