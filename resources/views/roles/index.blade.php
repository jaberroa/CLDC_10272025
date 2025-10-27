@extends('partials.layouts.master')

@section('title', 'Roles y Permisos | CLDCI')
@section('pagetitle', 'Gestión de Roles y Permisos')

@section('css')
<style>
    .rol-card {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .rol-card:hover {
        border-color: #0d6efd;
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .rol-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-shield-user-line me-2"></i>
                    Roles del Sistema
                </h5>
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i>
                    Nuevo Rol
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @forelse($roles as $rol)
                    <div class="col-xxl-3 col-lg-4 col-md-6">
                        <div class="rol-card card mb-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="rol-badge" style="background: {{ $rol->color }}20; color: {{ $rol->color }}">
                                        {{ $rol->nombre }}
                                    </div>
                                    <div class="ms-auto">
                                        @if($rol->activo)
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                        @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-muted mb-3 small">{{ $rol->descripcion ?? 'Sin descripción' }}</p>
                                
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span>
                                        <i class="ri-user-line me-1"></i>
                                        {{ $rol->usuarios_count }} usuarios
                                    </span>
                                    <span>
                                        <i class="ri-key-line me-1"></i>
                                        {{ $rol->permisos_count }} permisos
                                    </span>
                                </div>
                                
                                <div class="border-top pt-3">
                                    <span class="badge bg-primary-subtle text-primary">
                                        Nivel {{ $rol->nivel }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 pt-3 border-top">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('roles.show', $rol) }}" 
                                           class="btn btn-sm btn-soft-primary" title="Ver">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $rol) }}" 
                                           class="btn btn-sm btn-soft-warning" title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-soft-danger" 
                                                onclick="confirmarEliminar({{ $rol->id }}, '{{ $rol->nombre }}')"
                                                title="Eliminar">
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
                            <i class="ri-shield-user-line display-1 text-muted"></i>
                            <h5 class="mt-3">No hay roles creados</h5>
                            <p class="text-muted">Crea tu primer rol para comenzar</p>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary mt-3">
                                <i class="ri-add-line me-1"></i>
                                Crear Primer Rol
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                @if($roles->hasPages())
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $roles->links() }}
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
                <p class="mb-0">¿Está seguro que desea eliminar el rol <strong id="rolNombre"></strong>?</p>
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
    let rolIdEliminar = null;

    function confirmarEliminar(rolId, rolNombre) {
        rolIdEliminar = rolId;
        document.getElementById('rolNombre').textContent = rolNombre;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (rolIdEliminar) {
            const form = document.getElementById('deleteForm');
            form.action = `/roles/${rolIdEliminar}`;
            form.submit();
        }
    });
</script>
@endsection

