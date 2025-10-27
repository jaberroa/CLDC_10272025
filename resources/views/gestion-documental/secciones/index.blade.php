@extends('partials.layouts.master')

@section('title', 'Secciones Documentales | CLDCI')
@section('pagetitle', 'Secciones Documentales')

@section('css')
<style>
    .seccion-card {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .seccion-card:hover {
        border-color: #0d6efd;
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .seccion-icono {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-folders-line me-2"></i>
                    Secciones Documentales
                </h5>
                <a href="{{ route('gestion-documental.secciones.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i>
                    Nueva Sección
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @forelse($secciones as $seccion)
                    <div class="col-xxl-3 col-lg-4 col-md-6">
                        <div class="seccion-card card mb-0 h-100" 
                             onclick="window.location='{{ route('gestion-documental.secciones.show', $seccion) }}'">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="seccion-icono" style="background: {{ $seccion->color ?? '#0d6efd' }}20; color: {{ $seccion->color ?? '#0d6efd' }}">
                                        <i class="{{ $seccion->icono ?? 'ri-folder-line' }}"></i>
                                    </div>
                                    <div class="ms-auto">
                                        @if($seccion->activa)
                                        <span class="badge bg-success-subtle text-success">Activa</span>
                                        @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactiva</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <h5 class="card-title mb-2">{{ $seccion->nombre }}</h5>
                                <p class="text-muted mb-3 small">{{ Str::limit($seccion->descripcion, 80) }}</p>
                                
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>
                                        <i class="ri-folder-line me-1"></i>
                                        {{ $seccion->carpetas()->count() }} carpetas
                                    </span>
                                    <span>
                                        <i class="ri-file-line me-1"></i>
                                        {{ $seccion->documentos()->count() }} docs
                                    </span>
                                </div>
                                
                                <div class="mt-3 pt-3 border-top">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('gestion-documental.secciones.show', $seccion) }}" 
                                           class="btn btn-sm btn-soft-primary" onclick="event.stopPropagation()">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('gestion-documental.secciones.edit', $seccion) }}" 
                                           class="btn btn-sm btn-soft-warning" onclick="event.stopPropagation()">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-soft-danger" 
                                                onclick="confirmarEliminar({{ $seccion->id }}, '{{ $seccion->nombre }}', event)">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri-folder-open-line display-1 text-muted"></i>
                            <h5 class="mt-3">No hay secciones creadas</h5>
                            <p class="text-muted">Crea tu primera sección para organizar documentos</p>
                            <a href="{{ route('gestion-documental.secciones.create') }}" class="btn btn-primary mt-3">
                                <i class="ri-add-line me-1"></i>
                                Crear Primera Sección
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                @if($secciones->hasPages())
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $secciones->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="ri-delete-bin-line me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Está seguro que desea eliminar la sección <strong id="seccionNombre"></strong>?</p>
                <p class="text-danger small mt-2 mb-0">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="ri-delete-bin-line me-1"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('js')
<script>
    let seccionIdEliminar = null;

    function confirmarEliminar(seccionId, seccionNombre, event) {
        event.stopPropagation();
        seccionIdEliminar = seccionId;
        document.getElementById('seccionNombre').textContent = seccionNombre;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (seccionIdEliminar) {
            const form = document.getElementById('deleteForm');
            form.action = `/gestion-documental/secciones/${seccionIdEliminar}`;
            form.submit();
        }
    });
</script>
@endsection

