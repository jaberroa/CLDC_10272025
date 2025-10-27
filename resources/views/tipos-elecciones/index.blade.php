@extends('partials.layouts.master')

@section('title', 'Tipos de Elecciones | CLDCI')
@section('title-sub', 'Gestión de Tipos de Elecciones')
@section('pagetitle', 'Tipos de Elecciones')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="ri-list-check-2 me-2"></i>
                    Tipos de Elecciones
                </h4>
                @if(Schema::hasTable('tipos_elecciones'))
                    <a href="{{ route('tipos-elecciones.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>
                        Nuevo Tipo
                    </a>
                @else
                    <button class="btn btn-secondary" disabled title="Ejecuta php artisan migrate primero">
                        <i class="ri-lock-line me-1"></i>
                        Nuevo Tipo (Deshabilitado)
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if(!Schema::hasTable('tipos_elecciones'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><i class="ri-alert-line me-2"></i>Modo de Vista Previa</strong>
                        <p class="mb-0 mt-2">
                            La tabla <code>tipos_elecciones</code> no existe en la base de datos. 
                            Estás viendo datos de ejemplo. Para habilitar la funcionalidad completa, ejecuta:
                            <code class="d-block mt-2 bg-dark text-white p-2 rounded">php artisan migrate</code>
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-check-line me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="50">Orden</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th width="100" class="text-center">Estado</th>
                                <th width="150" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipos as $tipo)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $tipo->orden }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-{{ $tipo->color }}-subtle text-{{ $tipo->color }}">
                                                    <i class="{{ $tipo->icono }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $tipo->nombre }}</h6>
                                            <small class="text-muted">{{ $tipo->slug }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $tipo->descripcion ?? 'Sin descripción' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($tipo->activo)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="ri-checkbox-circle-line me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="ri-close-circle-line me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(Schema::hasTable('tipos_elecciones'))
                                        <div class="btn-group btn-group-sm" role="group">
                                              <button type="button" 
                                                      class="btn btn-soft-primary" 
                                                      onclick="abrirModalEditar({{ $tipo->id }}, '{{ $tipo->nombre }}', '{{ $tipo->descripcion }}', '{{ $tipo->icono }}', '{{ $tipo->color }}', {{ $tipo->orden }}, {{ $tipo->activo ? 'true' : 'false' }})"
                                                      title="Editar">
                                                  <i class="ri-edit-line"></i>
                                              </button>
                                            <button type="button" 
                                                    class="btn btn-soft-danger" 
                                                    onclick="eliminarTipo({{ $tipo->id }}, '{{ $tipo->nombre }}')"
                                                    title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="ri-lock-line me-1"></i>Vista previa
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="ri-inbox-line" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">No hay tipos de elecciones registrados</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tipos->hasPages())
                    <div class="mt-3">
                        {{ $tipos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if(Schema::hasTable('tipos_elecciones'))
<!-- Modal: Editar Tipo de Elección -->
<div class="modal fade" id="modalEditarTipo" tabindex="-1" aria-labelledby="modalEditarTipoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="modalEditarTipoLabel">
                    <i class="ri-edit-line me-2"></i>
                    Editar Tipo de Elección
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarTipo">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_edit_nombre" class="form-label">
                            Nombre del Tipo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="modal_edit_nombre" 
                               required 
                               placeholder="Ej: Elección de Vocal">
                    </div>

                    <div class="mb-3">
                        <label for="modal_edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" 
                                  id="modal_edit_descripcion" 
                                  rows="3" 
                                  placeholder="Describe el propósito..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_edit_icono" class="form-label">Icono</label>
                                <select class="form-select" id="modal_edit_icono">
                                    <option value="ri-checkbox-circle-line">📋 Checkbox</option>
                                    <option value="ri-team-line">👥 Team</option>
                                    <option value="ri-group-line">👨‍👩‍👧‍👦 Group</option>
                                    <option value="ri-star-line">⭐ Star</option>
                                    <option value="ri-user-line">👤 User</option>
                                    <option value="ri-building-line">🏢 Building</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_edit_color" class="form-label">Color</label>
                                <select class="form-select" id="modal_edit_color">
                                    <option value="primary">🔵 Azul</option>
                                    <option value="success">🟢 Verde</option>
                                    <option value="warning">🟡 Amarillo</option>
                                    <option value="danger">🔴 Rojo</option>
                                    <option value="info">🔵 Info</option>
                                    <option value="secondary">⚫ Gris</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_edit_orden" class="form-label">Orden</label>
                        <input type="number" 
                               class="form-control" 
                               id="modal_edit_orden" 
                               min="0" 
                               value="0">
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modal_edit_activo">
                        <label class="form-check-label" for="modal_edit_activo">Activo</label>
                    </div>

                    <!-- Vista Previa -->
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="ri-information-line me-2"></i>
                            Vista Previa
                        </h6>
                        <div class="d-flex align-items-center">
                            <span id="modalPreviewIcon" class="badge bg-secondary-subtle text-secondary me-2" style="font-size: 1.2rem; padding: 0.5rem;">
                                <i class="ri-question-line"></i>
                            </span>
                            <div>
                                <h6 class="mb-0" id="modalPreviewNombre">Nombre del Tipo</h6>
                                <small class="text-muted" id="modalPreviewDescripcion">Descripción del tipo</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnActualizarTipo">
                        <i class="ri-save-line me-1"></i>
                        Actualizar Tipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let tipoIdActual = null;

// Función para abrir modal de edición
function abrirModalEditar(id, nombre, descripcion, icono, color, orden, activo) {
    tipoIdActual = id;
    
    // Llenar formulario
    document.getElementById('modal_edit_nombre').value = nombre;
    document.getElementById('modal_edit_descripcion').value = descripcion || '';
    document.getElementById('modal_edit_icono').value = icono || 'ri-question-line';
    document.getElementById('modal_edit_color').value = color || 'secondary';
    document.getElementById('modal_edit_orden').value = orden || 0;
    document.getElementById('modal_edit_activo').checked = activo;
    
    // Actualizar vista previa
    actualizarVistaPreviaModal();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarTipo'));
    modal.show();
}

// Función para actualizar vista previa del modal
function actualizarVistaPreviaModal() {
    const nombre = document.getElementById('modal_edit_nombre').value || 'Nombre del Tipo';
    const descripcion = document.getElementById('modal_edit_descripcion').value || 'Descripción del tipo';
    const icono = document.getElementById('modal_edit_icono').value || 'ri-question-line';
    const color = document.getElementById('modal_edit_color').value || 'secondary';

    document.getElementById('modalPreviewNombre').textContent = nombre;
    document.getElementById('modalPreviewDescripcion').textContent = descripcion;
    
    const previewIconSpan = document.getElementById('modalPreviewIcon');
    previewIconSpan.innerHTML = `<i class="${icono}"></i>`;
    previewIconSpan.className = `badge bg-${color}-subtle text-${color} me-2`;
}

// Event listeners para vista previa en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const modalNombre = document.getElementById('modal_edit_nombre');
    const modalDescripcion = document.getElementById('modal_edit_descripcion');
    const modalIcono = document.getElementById('modal_edit_icono');
    const modalColor = document.getElementById('modal_edit_color');
    
    if (modalNombre) modalNombre.addEventListener('input', actualizarVistaPreviaModal);
    if (modalDescripcion) modalDescripcion.addEventListener('input', actualizarVistaPreviaModal);
    if (modalIcono) modalIcono.addEventListener('change', actualizarVistaPreviaModal);
    if (modalColor) modalColor.addEventListener('change', actualizarVistaPreviaModal);
});

// Manejar envío del formulario de edición
document.getElementById('formEditarTipo').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnActualizar = document.getElementById('btnActualizarTipo');
    const btnText = btnActualizar.innerHTML;
    
    // Deshabilitar botón
    btnActualizar.disabled = true;
    btnActualizar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...';
    
    const formData = {
        nombre: document.getElementById('modal_edit_nombre').value,
        descripcion: document.getElementById('modal_edit_descripcion').value,
        icono: document.getElementById('modal_edit_icono').value,
        color: document.getElementById('modal_edit_color').value,
        orden: parseInt(document.getElementById('modal_edit_orden').value),
        activo: document.getElementById('modal_edit_activo').checked ? 1 : 0,
        _method: 'PUT'
    };
    
    fetch(`/tipos-elecciones/${tipoIdActual}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || response.ok) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarTipo'));
            modal.hide();
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Tipo de elección actualizado exitosamente',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al actualizar el tipo de elección'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al actualizar el tipo de elección'
        });
    })
    .finally(() => {
        btnActualizar.disabled = false;
        btnActualizar.innerHTML = btnText;
    });
});

function eliminarTipo(id, nombre) {
    Swal.fire({
        title: '¿Eliminar tipo de elección?',
        html: `¿Estás seguro de eliminar el tipo <strong>${nombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/tipos-elecciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado', data.message, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Ocurrió un error al eliminar el tipo', 'error');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endsection

