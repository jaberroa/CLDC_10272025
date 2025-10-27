@extends('partials.layouts.master')

@section('title', 'Gestión de Carpetas | CLDCI')
@section('pagetitle', 'Estructura de Carpetas')

@section('css')
<style>
    .folder-tree {
        list-style: none;
        padding-left: 0;
    }
    
    .folder-tree ul {
        list-style: none;
        padding-left: 30px;
        margin-top: 5px;
        display: none; /* Oculto por defecto */
    }
    
    .folder-tree ul.show {
        display: block;
    }
    
    .folder-item {
        padding: 12px 15px;
        margin: 5px 0;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        transition: all 0.2s;
        cursor: pointer;
        background: white;
        position: relative;
    }
    
    .folder-item:hover {
        background: #f8f9fa;
        border-color: #0d6efd;
        transform: translateX(5px);
    }
    
    .folder-item.has-children {
        padding-left: 45px;
    }
    
    .folder-toggle {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .folder-toggle:hover {
        background: #0d6efd;
        color: white;
    }
    
    .folder-toggle.expanded {
        transform: translateY(-50%) rotate(90deg);
    }
    
    .folder-icon {
        font-size: 24px;
        margin-right: 10px;
    }
    
    .folder-info {
        display: flex;
        align-items: center;
        flex: 1;
    }
    
    .folder-name {
        font-weight: 500;
        font-size: 15px;
        flex: 1;
    }
    
    .folder-meta {
        font-size: 12px;
        color: #6c757d;
        margin-left: 15px;
    }
    
    .folder-actions {
        margin-left: 15px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .folder-item:hover .folder-actions {
        opacity: 1;
    }
    
    .breadcrumb-path {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 14px;
    }
    
    .nivel-badge {
        font-size: 10px;
        padding: 2px 8px;
        background: #e9ecef;
        border-radius: 12px;
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

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary-subtle rounded flex-shrink-0">
                        <i class="ri-folder-line fs-3 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0">Total Carpetas</p>
                        <h4 class="mb-0">{{ $estadisticas['total_carpetas'] }}</h4>
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
                        <p class="text-muted mb-0">Total Documentos</p>
                        <h4 class="mb-0">{{ $estadisticas['total_documentos'] }}</h4>
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
                        <p class="text-muted mb-0">Espacio Usado</p>
                        <h4 class="mb-0">{{ number_format($estadisticas['espacio_usado'] / 1024 / 1024, 2) }} MB</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toolbar -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <select class="form-select" onchange="filtrarPorSeccion(this.value)">
                    <option value="">Todas las secciones</option>
                    @foreach($secciones as $seccion)
                        <option value="{{ $seccion->id }}" {{ $seccionId == $seccion->id ? 'selected' : '' }}>
                            {{ $seccion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" onclick="mostrarModalNuevaCarpeta(null)">
                    <i class="ri-folder-add-line me-1"></i>
                    Nueva Carpeta Raíz
                </button>
                <button class="btn btn-soft-secondary" onclick="expandirTodo()">
                    <i class="ri-arrow-down-s-line me-1"></i>
                    Expandir Todo
                </button>
                <button class="btn btn-soft-secondary" onclick="contraerTodo()">
                    <i class="ri-arrow-up-s-line me-1"></i>
                    Contraer Todo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Árbol de Carpetas -->
<div class="card">
    <div class="card-body">
        @if($carpetas->isEmpty())
            <div class="text-center py-5">
                <i class="ri-folder-open-line display-1 text-muted"></i>
                <h5 class="mt-3">No hay carpetas creadas</h5>
                <p class="text-muted">Comienza creando una carpeta raíz</p>
                <button class="btn btn-primary mt-2" onclick="mostrarModalNuevaCarpeta(null)">
                    <i class="ri-folder-add-line me-1"></i>
                    Crear Primera Carpeta
                </button>
            </div>
        @else
            <ul class="folder-tree">
                @foreach($carpetas as $carpeta)
                    @include('gestion-documental.carpetas.partials.folder-item', ['carpeta' => $carpeta])
                @endforeach
            </ul>
        @endif
    </div>
</div>

<!-- Modal Nueva Carpeta -->
<div class="modal fade" id="modalNuevaCarpeta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-folder-add-line me-2"></i>
                    Nueva Carpeta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevaCarpeta" onsubmit="crearCarpeta(event)">
                <div class="modal-body">
                    <input type="hidden" id="carpeta_padre_id" name="carpeta_padre_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Sección <span class="text-danger">*</span></label>
                        <select class="form-select" name="seccion_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}" {{ $seccionId == $seccion->id ? 'selected' : '' }}>
                                    {{ $seccion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Carpeta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej: Contratos 2025" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="color" id="color1" value="#FFC107" checked>
                            <label class="btn btn-sm" for="color1" style="background: #FFC107; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color2" value="#0d6efd">
                            <label class="btn btn-sm" for="color2" style="background: #0d6efd; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color3" value="#198754">
                            <label class="btn btn-sm" for="color3" style="background: #198754; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color4" value="#dc3545">
                            <label class="btn btn-sm" for="color4" style="background: #dc3545; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color5" value="#6f42c1">
                            <label class="btn btn-sm" for="color5" style="background: #6f42c1; width: 40px; height: 40px;"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>
                        Crear Carpeta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Toggle expandir/contraer carpeta
    function toggleFolder(id) {
        const ul = document.querySelector(`#folder-${id} > ul`);
        const toggle = document.querySelector(`#folder-${id} .folder-toggle`);
        
        if (ul) {
            ul.classList.toggle('show');
            toggle.classList.toggle('expanded');
        }
    }
    
    // Expandir todas las carpetas
    function expandirTodo() {
        document.querySelectorAll('.folder-tree ul').forEach(ul => {
            ul.classList.add('show');
        });
        document.querySelectorAll('.folder-toggle').forEach(toggle => {
            toggle.classList.add('expanded');
        });
    }
    
    // Contraer todas las carpetas
    function contraerTodo() {
        document.querySelectorAll('.folder-tree ul').forEach(ul => {
            ul.classList.remove('show');
        });
        document.querySelectorAll('.folder-toggle').forEach(toggle => {
            toggle.classList.remove('expanded');
        });
    }
    
    // Filtrar por sección
    function filtrarPorSeccion(seccionId) {
        const url = new URL(window.location);
        if (seccionId) {
            url.searchParams.set('seccion_id', seccionId);
        } else {
            url.searchParams.delete('seccion_id');
        }
        window.location = url;
    }
    
    // Abrir carpeta (navegar)
    function abrirCarpeta(id) {
        window.location = `/gestion-documental/carpetas/${id}`;
    }
    
    // Mostrar modal nueva carpeta
    function mostrarModalNuevaCarpeta(padreId) {
        document.getElementById('carpeta_padre_id').value = padreId || '';
        const modal = new bootstrap.Modal(document.getElementById('modalNuevaCarpeta'));
        modal.show();
    }
    
    // Crear carpeta
    function crearCarpeta(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            nombre: formData.get('nombre'),
            descripcion: formData.get('descripcion'),
            color: formData.get('color'),
            carpeta_padre_id: formData.get('carpeta_padre_id') || null,
            seccion_id: formData.get('seccion_id')
        };
        
        // Validar campos requeridos
        if (!data.nombre || !data.seccion_id) {
            showErrorToast('Por favor completa todos los campos requeridos');
            return;
        }
        
        fetch('{{ route("gestion-documental.carpetas.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error al crear la carpeta');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message || 'Carpeta creada exitosamente');
                $('#modalNuevaCarpeta').modal('hide');
                location.reload();
            } else {
                showErrorToast(data.message || 'Error al crear la carpeta');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            showErrorToast(error.message || 'Error al crear la carpeta');
        });
    }
    
    // Eliminar carpeta
    function eliminarCarpeta(id, nombre) {
        if (!confirm(`¿Estás seguro de eliminar la carpeta "${nombre}" y todo su contenido?`)) return;
        
        fetch(`{{ route("gestion-documental.carpetas.index") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(data => {
            showSuccessToast('Carpeta eliminada exitosamente');
            location.reload();
        })
        .catch(error => {
            showErrorToast('Error al eliminar la carpeta');
        });
    }
</script>
@endsection
