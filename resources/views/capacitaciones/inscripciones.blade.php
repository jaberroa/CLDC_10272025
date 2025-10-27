@extends('partials.layouts.master')

@section('title', 'Mis Inscripciones | CLDCI')
@section('title-sub', 'Gestión de Capacitaciones')
@section('pagetitle', 'Mis Inscripciones')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
                <!-- Estadísticas de Inscripciones -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-primary-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-user-check-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">{{ $estadisticas['total_inscripciones'] }}</h6>
                                <p class="text-muted mb-0 small">Total Inscripciones</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-success-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-check-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">{{ $estadisticas['confirmadas'] }}</h6>
                                <p class="text-muted mb-0 small">Confirmadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-warning-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-time-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">{{ $estadisticas['pendientes'] }}</h6>
                                <p class="text-muted mb-0 small">Pendientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-info-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-user-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">{{ $estadisticas['asistieron'] }}</h6>
                                <p class="text-muted mb-0 small">Asistieron</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-danger-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-user-unfollow-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">{{ $estadisticas['no_asistieron'] }}</h6>
                                <p class="text-muted mb-0 small">No Asistieron</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card border-0 bg-secondary-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-money-dollar-circle-line text-white fs-4"></i>
                                </div>
                                <h6 class="mb-1">RD$ {{ number_format($estadisticas['ingresos_totales'], 0) }}</h6>
                                <p class="text-muted mb-0 small">Ingresos Totales</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros Globales -->
                <x-global-filter
                    title="Filtros de Búsqueda"
                    description="Refine los resultados utilizando los filtros disponibles"
                    icon="ri-search-line"
                    form-id="inscripciones-filters-form"
                    form-action="{{ route('capacitaciones.inscripciones') }}"
                    clear-url="{{ route('capacitaciones.inscripciones') }}"
                    submit-label="Buscar"
                    clear-label="Limpiar"
                    variant="default"
                    :filters="[
                        [
                            'name' => 'buscar',
                            'label' => 'Buscar',
                            'type' => 'text',
                            'placeholder' => 'Nombre, carnet o curso',
                            'col' => 'col-md-4'
                        ],
                        [
                            'name' => 'estado',
                            'label' => 'Estado',
                            'type' => 'select',
                            'placeholder' => 'Todos los estados',
                            'col' => 'col-md-3',
                            'options' => [
                                'confirmada' => 'Confirmada',
                                'pendiente' => 'Pendiente'
                            ]
                        ],
                        [
                            'name' => 'modalidad',
                            'label' => 'Modalidad',
                            'type' => 'select',
                            'placeholder' => 'Todas las modalidades',
                            'col' => 'col-md-3',
                            'options' => [
                                'presencial' => 'Presencial',
                                'virtual' => 'Virtual',
                                'mixta' => 'Mixta'
                            ]
                        ],
                        [
                            'name' => 'asistencia',
                            'label' => 'Asistencia',
                            'type' => 'select',
                            'placeholder' => 'Todas las asistencias',
                            'col' => 'col-md-2',
                            'options' => [
                                'si' => 'Asistió',
                                'no' => 'No Asistió'
                            ]
                        ]
                    ]"
                />

                <!-- Controles de Vista -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <h6 class="mb-0 text-muted">
                                    <i class="ri-eye-line me-1"></i>
                                    Vista
                                </h6>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">Mostrar:</span>
                                <div class="per-page-selector">
                                    <select id="perPageSelect" name="per_page" class="form-select form-select-sm per-page-select" onchange="changePageSize(this.value)" aria-label="Seleccionar número de elementos por página">
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="all">Todas</option>
                                    </select>
                                </div>
                                <span class="text-muted small">por página</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Inscripciones -->
                <div class="col-xxl-12">
                    <div class="card shadow-sm">
                        <div class="card-header miembros-index-header">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <a href="{{ route('capacitaciones.create') }}" class="btn btn-agregar">
                                        <i class="ri-user-add-line"></i>
                                        <span>Agregar Inscripción</span>
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title">
                                        <i class="ri-group-line"></i>
                                        Mis Inscripciones
                                    </h4>
                                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                                        Gestione y administre todas las inscripciones a cursos de capacitación
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="dropdown" id="bulkActionsDropdown" style="display: none;">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-checkbox-multiple-line me-1"></i> Acciones Masivas
                                            <span id="selectedCount" class="badge bg-light text-dark ms-1">0</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="bulkAction('export')">
                                                <i class="ri-download-line me-2"></i> Exportar Seleccionados
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="bulkAction('print')">
                                                <i class="ri-printer-line me-2"></i> Imprimir Seleccionados
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="bulkAction('email')">
                                                <i class="ri-mail-line me-2"></i> Enviar Email
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="bulkAction('status')">
                                                <i class="ri-user-settings-line me-2"></i> Cambiar Estado
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                                <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionados
                                            </a></li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarInscripciones()">
                                        <i class="ri-download-line me-1"></i> Exportar
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm" onclick="imprimirLista()">
                                        <i class="ri-printer-line me-1"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-body">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap inscripciones-table">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        <label class="form-check-label" for="selectAll">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </th>
                                <th class="sortable" data-sort="miembro">
                                    Miembro <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="capacitacion">
                                    Capacitación <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_inscripcion">
                                    Fecha Inscripción <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="modalidad">
                                    Modalidad <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="costo">
                                    Costo <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="estado">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="asistencia">
                                    Asistencia <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inscripciones as $inscripcion)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input inscripcion-checkbox" type="checkbox" 
                                               value="{{ $inscripcion->id }}" 
                                               id="inscripcion_{{ $inscripcion->id }}"
                                               onchange="updateSelectAllState()">
                                        <label class="form-check-label" for="inscripcion_{{ $inscripcion->id }}">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                <i class="ri-user-line fs-12"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0">{{ $inscripcion->miembro->nombre_completo }}</h6>
                                            <p class="text-muted mb-0 fs-12">
                                                <i class="ri-id-card-line me-1"></i>{{ $inscripcion->miembro->numero_carnet }}
                                            </p>
                                            <p class="text-muted mb-0 fs-12">
                                                <i class="ri-mail-line me-1"></i>{{ $inscripcion->miembro->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $inscripcion->capacitacion->titulo }}</h6>
                                        <small class="text-muted">{{ $inscripcion->capacitacion->fecha_inicio->format('d/m/Y H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center modalidad-container">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs bg-{{ $inscripcion->capacitacion->modalidad === 'presencial' ? 'primary' : ($inscripcion->capacitacion->modalidad === 'virtual' ? 'info' : 'warning') }}-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="ri-{{ $inscripcion->capacitacion->modalidad === 'presencial' ? 'building' : ($inscripcion->capacitacion->modalidad === 'virtual' ? 'computer' : 'mix') }}-line text-{{ $inscripcion->capacitacion->modalidad === 'presencial' ? 'primary' : ($inscripcion->capacitacion->modalidad === 'virtual' ? 'info' : 'warning') }} fs-10"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-semibold text-dark">{{ ucfirst($inscripcion->capacitacion->modalidad) }}</span>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($inscripcion->capacitacion->modalidad) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">RD$ {{ number_format($inscripcion->capacitacion->costo, 0) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center estado-container">
                                        <div class="flex-shrink-0 me-2">
                                            @if($inscripcion->estado === 'confirmada')
                                                <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-check-line text-success fs-10"></i>
                                                </div>
                                            @else
                                                <div class="avatar-xs bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-time-line text-warning fs-10"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="badge bg-{{ $inscripcion->estado === 'confirmada' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $inscripcion->estado === 'confirmada' ? 'success' : 'warning' }} border border-{{ $inscripcion->estado === 'confirmada' ? 'success' : 'warning' }} border-opacity-25 fw-semibold">
                                                {{ ucfirst($inscripcion->estado) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $inscripcion->estado === 'confirmada' ? 'Inscripción confirmada' : 'Pendiente de confirmación' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center asistencia-container">
                                        <div class="flex-shrink-0 me-2">
                                            @if($inscripcion->asistio)
                                                <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-check-line text-success fs-10"></i>
                                                </div>
                                            @else
                                                <div class="avatar-xs bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-close-line text-danger fs-10"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="badge bg-{{ $inscripcion->asistio ? 'success' : 'danger' }} bg-opacity-10 text-{{ $inscripcion->asistio ? 'success' : 'danger' }} border border-{{ $inscripcion->asistio ? 'success' : 'danger' }} border-opacity-25 fw-semibold">
                                                {{ $inscripcion->asistio ? 'Asistió' : 'No Asistió' }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $inscripcion->asistio ? 'Participó en el curso' : 'No participó' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <!-- Primera fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Ver Detalles -->
                                            <button type="button" 
                                                    class="btn btn-soft-primary btn-sm" 
                                                    title="Ver Detalles"
                                                    onclick="verDetalles({{ $inscripcion->id }})">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <!-- Editar -->
                                            <button type="button" 
                                                    class="btn btn-soft-info btn-sm" 
                                                    title="Editar"
                                                    onclick="editarInscripcion({{ $inscripcion->id }})">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        </div>
                                        <!-- Segunda fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Eliminar -->
                                            <button type="button" 
                                                    class="btn btn-soft-danger btn-sm" 
                                                    title="Eliminar"
                                                    onclick="deleteInscripcion({{ $inscripcion->id }}, '{{ $inscripcion->miembro->nombre_completo }}')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-user-check-line fs-1 mb-3 d-block"></i>
                                        <h5>No hay inscripciones registradas</h5>
                                        <p>No se encontraron inscripciones a cursos de capacitación.</p>
                                        <a href="{{ route('capacitaciones.create') }}" class="btn btn-primary">
                                            <i class="ri-add-line me-1"></i> Crear Nueva Inscripción
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div class="cldci-pagination-container">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            <span class="text-muted">Mostrando <strong>{{ $inscripciones->count() }}</strong> de <strong>{{ $inscripciones->count() }}</strong> inscripciones</span>
                        </div>
                        <div class="cldci-pagination">
                            <button class="pagination-btn pagination-btn-nav" disabled>
                                <i class="ri-arrow-left-s-line"></i>
                            </button>
                            <div class="pagination-numbers">
                                <button class="pagination-btn pagination-number active">1</button>
                            </div>
                            <button class="pagination-btn pagination-btn-nav" disabled>
                                <i class="ri-arrow-right-s-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Funciones de eliminación
function deleteInscripcion(inscripcionId, miembroNombre) {
    if (confirm(`¿Está seguro de eliminar la inscripción de "${miembroNombre}"?`)) {
        // Simular eliminación
        showSuccessToast(`Inscripción de "${miembroNombre}" eliminada exitosamente`);
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }
}

// Funciones de acción
function verDetalles(inscripcionId) {
    console.log('Ver detalles de inscripción:', inscripcionId);
}

function editarInscripcion(inscripcionId) {
    console.log('Editar inscripción:', inscripcionId);
}

function exportarInscripciones() {
    console.log('Exportar inscripciones');
}

function imprimirLista() {
    console.log('Imprimir lista');
}

// Funciones de selección múltiple
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.inscripcion-checkbox');
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.inscripcion-checkbox');
    const checkedBoxes = document.querySelectorAll('.inscripcion-checkbox:checked');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCount = document.getElementById('selectedCount');
    
    // Actualizar estado del checkbox principal
    selectAllCheckbox.checked = checkedBoxes.length === rowCheckboxes.length;
    selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
    
    // Mostrar/ocultar acciones masivas
    if (checkedBoxes.length > 0) {
        bulkActionsDropdown.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActionsDropdown.style.display = 'none';
    }
}

// Funciones de acciones masivas
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.inscripcion-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos una inscripción');
        return;
    }
    
    switch(action) {
        case 'export':
            console.log('Exportar seleccionados:', selectedIds);
            break;
        case 'print':
            console.log('Imprimir seleccionados:', selectedIds);
            break;
        case 'email':
            console.log('Enviar email a seleccionados:', selectedIds);
            break;
        case 'status':
            console.log('Cambiar estado de seleccionados:', selectedIds);
            break;
        case 'delete':
            if (confirm(`¿Está seguro de eliminar ${selectedIds.length} inscripción(es) seleccionada(s)?`)) {
                console.log('Eliminar seleccionados:', selectedIds);
            }
            break;
    }
}

// Funciones de toast
function showSuccessToast(message) {
    // Implementar toast de éxito
    console.log('Success:', message);
}

function showErrorToast(message) {
    // Implementar toast de error
    console.log('Error:', message);
}

// Cambiar tamaño de página
function changePageSize(value) {
    console.log('Cambiar tamaño de página a:', value);
    // Implementar lógica de paginación
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar estado de selección
    updateSelectAllState();
});
</script>
@endsection
