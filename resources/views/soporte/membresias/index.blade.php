@extends('partials.layouts.master')

@section('title', 'Gestión de Membresías | CLDCI')
@section('pagetitle', 'Gestión de Estados y Tipos de Membresía')

@section('content')
<div class="row">
    <!-- Estados de Membresía -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    Estados de Membresía
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoEstado">
                    <i class="ri-add-line me-1"></i>
                    Nuevo Estado
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Color</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estados as $estado)
                            <tr>
                                <td><strong>{{ $estado->nombre }}</strong></td>
                                <td>{{ $estado->descripcion ?? '-' }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $estado->color }};">
                                        {{ $estado->color }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                onclick="editarEstado({{ $estado->id }}, '{{ $estado->nombre }}', '{{ $estado->descripcion }}', '{{ $estado->color }}')">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteEstado({{ $estado->id }}, '{{ addslashes($estado->nombre) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay estados registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipos de Membresía -->
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-group-line me-2"></i>
                    Tipos de Membresía
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoTipo">
                    <i class="ri-add-line me-1"></i>
                    Nuevo Tipo
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipos as $tipo)
                            <tr>
                                <td><strong>{{ $tipo->nombre }}</strong></td>
                                <td>{{ $tipo->descripcion ?? '-' }}</td>
                                <td>
                                    @if($tipo->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                onclick="editarTipo({{ $tipo->id }}, '{{ $tipo->nombre }}', '{{ $tipo->descripcion }}', '{{ $tipo->color }}', {{ $tipo->activo ? 'true' : 'false' }})">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteTipo({{ $tipo->id }}, '{{ addslashes($tipo->nombre) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay tipos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo/Editar Estado -->
<div class="modal fade" id="modalNuevoEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstadoTitle">Nuevo Estado de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEstado" method="POST" action="{{ route('soporte.membresias.estado.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="estado_id" name="estado_id">
                    
                    <div class="mb-3">
                        <label for="estado_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="estado_nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="estado_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="estado_descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="estado_color" class="form-label">Color (Hex)</label>
                        <input type="color" class="form-control form-control-color" id="estado_color" name="color" value="#007bff">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nuevo/Editar Tipo -->
<div class="modal fade" id="modalNuevoTipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipoTitle">Nuevo Tipo de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTipo" method="POST" action="{{ route('soporte.membresias.tipo.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="tipo_id" name="tipo_id">
                    
                    <div class="mb-3">
                        <label for="tipo_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tipo_nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="tipo_descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_color" class="form-label">Color (Hex)</label>
                        <input type="color" class="form-control form-control-color" id="tipo_color" name="color" value="#007bff">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tipo_activo" name="activo" value="1" checked>
                            <label class="form-check-label" for="tipo_activo">
                                Activo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de Confirmación de Eliminación --}}
@include('components.modals.delete-confirmation')

@endsection

@section('js')
<script>
function editarEstado(id, nombre, descripcion, color) {
    document.getElementById('modalEstadoTitle').textContent = 'Editar Estado de Membresía';
    document.getElementById('formEstado').action = '{{ route("soporte.membresias.estado.update", ":id") }}'.replace(':id', id);
    document.getElementById('formEstado').innerHTML += '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('estado_nombre').value = nombre;
    document.getElementById('estado_descripcion').value = descripcion || '';
    document.getElementById('estado_color').value = color || '#007bff';
    new bootstrap.Modal(document.getElementById('modalNuevoEstado')).show();
}

function editarTipo(id, nombre, descripcion, color, activo) {
    document.getElementById('modalTipoTitle').textContent = 'Editar Tipo de Membresía';
    const form = document.getElementById('formTipo');
    form.action = '{{ route("soporte.membresias.tipo.update", ":id") }}'.replace(':id', id);
    
    // Agregar method PUT si no existe
    if (!form.querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    }
    
    document.getElementById('tipo_nombre').value = nombre;
    document.getElementById('tipo_descripcion').value = descripcion || '';
    document.getElementById('tipo_color').value = color || '#007bff';
    document.getElementById('tipo_activo').checked = activo;
    new bootstrap.Modal(document.getElementById('modalNuevoTipo')).show();
}

// Resetear modales al cerrar
document.getElementById('modalNuevoEstado').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formEstado').reset();
    document.getElementById('formEstado').action = '{{ route("soporte.membresias.estado.store") }}';
    document.getElementById('modalEstadoTitle').textContent = 'Nuevo Estado de Membresía';
    const methodInput = document.getElementById('formEstado').querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
});

document.getElementById('modalNuevoTipo').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formTipo').reset();
    document.getElementById('formTipo').action = '{{ route("soporte.membresias.tipo.store") }}';
    document.getElementById('modalTipoTitle').textContent = 'Nuevo Tipo de Membresía';
    const methodInput = document.getElementById('formTipo').querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    document.getElementById('tipo_activo').checked = true;
});

// Funciones para eliminar con modal de confirmación
function deleteEstado(estadoId, estadoNombre) {
    const modalText = document.getElementById('deleteConfirmationText');
    if (modalText) {
        modalText.innerHTML = `¿Está seguro de eliminar el estado <strong>"${estadoNombre}"</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`;
    }
    
    const modalElement = document.getElementById('deleteConfirmationModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("soporte.membresias.estado.destroy", ":id") }}'.replace(':id', estadoId);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    };
}

function deleteTipo(tipoId, tipoNombre) {
    const modalText = document.getElementById('deleteConfirmationText');
    if (modalText) {
        modalText.innerHTML = `¿Está seguro de eliminar el tipo <strong>"${tipoNombre}"</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`;
    }
    
    const modalElement = document.getElementById('deleteConfirmationModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("soporte.membresias.tipo.destroy", ":id") }}'.replace(':id', tipoId);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    };
}

// Resetear el onclick del botón de confirmación al cerrar el modal
document.getElementById('deleteConfirmationModal').addEventListener('hidden.bs.modal', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = null;
});
</script>
@endsection

