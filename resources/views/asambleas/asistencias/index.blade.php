@extends('partials.layouts.master')

@section('title', 'Asistencias de Asambleas | CLDCI')
@section('title-sub', 'Gestión de Asistencias')
@section('pagetitle', 'Asistencias de Asambleas')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/gridjs.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">

<style>
/* Estilos específicos para botones de acciones en asistencias */
.table-responsive .btn-soft-primary,
.table-responsive .btn-soft-info,
.table-responsive .btn-soft-success,
.table-responsive .btn-soft-warning,
.table-responsive .btn-soft-danger,
.table-responsive .btn-soft-secondary {
    border: none !important;
    background: transparent !important;
    padding: 0.375rem !important;
    border-radius: 0.375rem !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 2rem !important;
    height: 2rem !important;
    flex-shrink: 0 !important;
}

/* Colores específicos para cada acción */
.table-responsive .btn-soft-primary {
    color: #0d6efd !important;
}

.table-responsive .btn-soft-primary:hover {
    background: rgba(13, 110, 253, 0.1) !important;
    color: #0a58ca !important;
    transform: scale(1.05) !important;
}

.table-responsive .btn-soft-info {
    color: #0dcaf0 !important;
}

.table-responsive .btn-soft-info:hover {
    background: rgba(13, 202, 240, 0.1) !important;
    color: #0aa2c0 !important;
    transform: scale(1.05) !important;
}

.table-responsive .btn-soft-success {
    color: #198754 !important;
}

.table-responsive .btn-soft-success:hover {
    background: rgba(25, 135, 84, 0.1) !important;
    color: #146c43 !important;
    transform: scale(1.05) !important;
}

.table-responsive .btn-soft-warning {
    color: #ffc107 !important;
}

.table-responsive .btn-soft-warning:hover {
    background: rgba(255, 193, 7, 0.1) !important;
    color: #b45309 !important;
    transform: scale(1.05) !important;
}

.table-responsive .btn-soft-danger {
    color: #dc3545 !important;
}

.table-responsive .btn-soft-danger:hover {
    background: rgba(220, 53, 69, 0.1) !important;
    color: #b02a37 !important;
    transform: scale(1.05) !important;
}

.table-responsive .btn-soft-secondary {
    color: #6c757d !important;
}

.table-responsive .btn-soft-secondary:hover {
    background: rgba(108, 117, 125, 0.1) !important;
    color: #495057 !important;
    transform: scale(1.05) !important;
}

/* Iconos dentro de los botones */
.table-responsive .btn i {
    font-size: 0.875rem !important;
    line-height: 1 !important;
}

/* Tooltips */
.table-responsive [data-bs-toggle="tooltip"] {
    cursor: pointer !important;
}

/* Espaciado entre botones */
.table-responsive .d-flex.gap-1 {
    gap: 0.25rem !important;
    flex-wrap: nowrap !important;
    justify-content: flex-start !important;
}

/* Espaciado entre filas de iconos */
.table-responsive .d-flex.flex-column.gap-1 {
    gap: 0.25rem !important;
}

/* Asegurar que la columna de acciones tenga suficiente ancho */
.table-responsive th:last-child,
.table-responsive td:last-child {
    min-width: 150px !important;
    width: 150px !important;
}

/* Paginación CLDCI */
.cldci-pagination-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    border: 1px solid #e9ecef;
}

.cldci-pagination {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.25rem;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.375rem 0.5rem;
    border: 1px solid #dee2e6;
    background: #fff;
    color: #6c757d;
    text-decoration: none;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
    font-size: 0.875rem;
    min-width: 2rem;
    height: 2rem;
}

.pagination-btn:hover:not(:disabled) {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.pagination-btn.active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-btn-nav {
    padding: 0.375rem;
}

.pagination-numbers {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin: 0 0.5rem;
}

.pagination-ellipsis {
    padding: 0.375rem 0.25rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.pagination-info {
    font-size: 0.875rem;
}

.pagination-progress .progress {
    background-color: #e9ecef;
    border-radius: 2px;
}

.pagination-progress .progress-bar {
    transition: width 0.3s ease;
}

/* ==========================================
   PER PAGE SELECTOR - DISEÑO COMPACTO
   ========================================== */

.per-page-selector {
    position: relative;
    display: inline-block;
}

.per-page-select {
    min-width: 70px !important;
    width: 70px !important;
    height: 32px !important;
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    background: #fff !important;
    color: #495057 !important;
    transition: all 0.2s ease !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    cursor: pointer !important;
    text-align: center !important;
    font-weight: 500 !important;
}

.per-page-select:hover {
    border-color: #adb5bd !important;
    background: #f8f9fa !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

.per-page-select:focus {
    outline: none !important;
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
}

/* Icono personalizado para el select */
.per-page-selector::after {
    content: "\ea4e" !important;
    font-family: 'remixicon', sans-serif !important;
    position: absolute !important;
    top: 50% !important;
    right: 0.375rem !important;
    transform: translateY(-50%) !important;
    pointer-events: none !important;
    color: #6c757d !important;
    font-size: 0.75rem !important;
    line-height: 0 !important;
    z-index: 2 !important;
    transition: all 0.2s ease !important;
}

.per-page-selector:hover::after {
    color: #0d6efd !important;
}

/* Contenedor de controles de vista */
.d-flex.justify-content-between.align-items-center {
    background: rgba(248, 249, 250, 0.8) !important;
    border-radius: 0.5rem !important;
    padding: 0.75rem 1rem !important;
    border: 1px solid #e9ecef !important;
    backdrop-filter: blur(10px) !important;
}

/* Responsive para per page selector */
@media (max-width: 768px) {
    .per-page-select {
        min-width: 60px !important;
        width: 60px !important;
        font-size: 0.7rem !important;
    }
    
    .per-page-selector::after {
        right: 0.25rem !important;
        font-size: 0.7rem !important;
    }
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-body table-body">
                <!-- Estadísticas -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="card border-0 bg-primary-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-primary-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-user-line text-primary fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-primary">{{ $estadisticas['total'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Total Asistencias</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-info-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-info-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-checkbox-circle-line text-info fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-info">{{ $estadisticas['confirmadas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Confirmadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-success-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-success-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-user-check-line text-success fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-success">{{ $estadisticas['presentes'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Presentes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-danger-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-danger-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-user-unfollow-line text-danger fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-danger">{{ $estadisticas['ausentes'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Ausentes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-warning-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-warning-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-time-line text-warning fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-warning">{{ $estadisticas['tardanzas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Tardanzas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-purple-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-purple-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-calendar-today-line text-purple fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-purple">{{ $estadisticas['hoy'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Hoy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros de Búsqueda -->
                <x-global-filter
                    title="Filtros de Búsqueda"
                    description="Refine los resultados utilizando los filtros disponibles"
                    icon="ri-search-line"
                    form-id="asistencia-filters-form"
                    form-action="{{ route('asambleas.asistencias.index') }}"
                    clear-url="{{ route('asambleas.asistencias.index') }}"
                    submit-label="Buscar"
                    clear-label="Limpiar"
                    variant="default"
                    :filters="[
                        [
                            'name' => 'buscar',
                            'label' => 'Buscar',
                            'type' => 'text',
                            'placeholder' => 'Nombre, apellido o cédula',
                            'col' => 'col-md-3'
                        ],
                        [
                            'name' => 'asamblea_id',
                            'label' => 'Asamblea',
                            'type' => 'select',
                            'placeholder' => 'Todas las asambleas',
                            'col' => 'col-md-3',
                            'options' => $asambleas->pluck('titulo', 'id')->toArray()
                        ],
                        [
                            'name' => 'estado',
                            'label' => 'Estado',
                            'type' => 'select',
                            'placeholder' => 'Todos los estados',
                            'col' => 'col-md-2',
                            'options' => [
                                'confirmada' => 'Confirmada',
                                'presente' => 'Presente',
                                'ausente' => 'Ausente',
                                'tardanza' => 'Tardanza'
                            ]
                        ],
                        [
                            'name' => 'fecha_desde',
                            'label' => 'Desde',
                            'type' => 'date',
                            'placeholder' => 'Fecha desde',
                            'col' => 'col-md-2'
                        ],
                        [
                            'name' => 'fecha_hasta',
                            'label' => 'Hasta',
                            'type' => 'date',
                            'placeholder' => 'Fecha hasta',
                            'col' => 'col-md-2'
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
                                    Mostrando {{ $asistencias->count() }} de {{ $asistencias->total() }} asistencias
                                </h6>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Mostrar:</span>
                                    <div class="per-page-selector">
                                        <select id="perPageSelect" name="per_page" class="form-select form-select-sm per-page-select" onchange="changePageSize(this.value)" aria-label="Seleccionar número de elementos por página">
                                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                                            <option value="all" {{ request('per_page', 25) == 'all' ? 'selected' : '' }}>Todas</option>
                                        </select>
                                    </div>
                                    <span class="text-muted small">por página</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Asistencias -->
                <div class="col-xxl-12">
                    <div class="card shadow-sm">
                        <div class="card-header miembros-index-header">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <a href="{{ route('asambleas.asistencias.create') }}" class="btn btn-agregar">
                                        <i class="ri-user-add-line"></i>
                                        <span>Registrar Asistencia</span>
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title">
                                        <i class="ri-user-line"></i>
                                        Lista de Asistencias
                                    </h4>
                                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                                        Gestión de asistencias a asambleas del sistema
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
                                            <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                                <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionados
                                            </a></li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarAsistencias()">
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
                                <table class="table table-hover text-nowrap miembros-table" style="min-width: 1000px;">
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
                                            <th class="sortable" data-sort="asamblea">
                                                Asamblea <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th class="sortable" data-sort="fecha_asistencia">
                                                Fecha <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th class="sortable" data-sort="estado">
                                                Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asistencias as $asistencia)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input asistencia-checkbox" type="checkbox" 
                                                               value="{{ $asistencia->id }}" 
                                                               id="asistencia_{{ $asistencia->id }}"
                                                               onchange="updateSelectAllState()">
                                                        <label class="form-check-label" for="asistencia_{{ $asistencia->id }}">
                                                            <i class="ri-checkbox-line"></i>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="ri-user-line text-primary fs-12"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $asistencia->miembro->nombre }} {{ $asistencia->miembro->apellido }}</h6>
                                                            <p class="text-muted mb-0 fs-12">
                                                                <i class="ri-mail-line me-1"></i>{{ $asistencia->miembro->email }}
                                                            </p>
                                                            <p class="text-muted mb-0 fs-12">
                                                                <i class="ri-phone-line me-1"></i>{{ $asistencia->miembro->telefono }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs bg-info-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="ri-calendar-line text-info fs-12"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $asistencia->asamblea->titulo }}</h6>
                                                            <p class="text-muted mb-0 fs-12">
                                                                <i class="ri-map-pin-line me-1"></i>{{ $asistencia->asamblea->lugar }}
                                                            </p>
                                                            <p class="text-muted mb-0 fs-12">
                                                                <i class="ri-time-line me-1"></i>{{ $asistencia->asamblea->fecha_asamblea->format('d/m/Y H:i') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $asistencia->fecha_asistencia->format('d/m/Y') }}</span>
                                                        @if($asistencia->hora_llegada)
                                                            <small class="text-muted">{{ $asistencia->hora_llegada }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center estado-membresia-container">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs bg-{{ $asistencia->estado === 'confirmada' ? 'info' : ($asistencia->estado === 'presente' ? 'success' : ($asistencia->estado === 'ausente' ? 'danger' : 'warning')) }}-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="ri-{{ $asistencia->estado === 'confirmada' ? 'checkbox-circle' : ($asistencia->estado === 'presente' ? 'user-check' : ($asistencia->estado === 'ausente' ? 'user-unfollow' : 'time')) }}-line text-{{ $asistencia->estado === 'confirmada' ? 'info' : ($asistencia->estado === 'presente' ? 'success' : ($asistencia->estado === 'ausente' ? 'danger' : 'warning')) }} fs-10"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <span class="badge bg-{{ $asistencia->estado === 'confirmada' ? 'info' : ($asistencia->estado === 'presente' ? 'success' : ($asistencia->estado === 'ausente' ? 'danger' : 'warning')) }}-subtle text-{{ $asistencia->estado === 'confirmada' ? 'info' : ($asistencia->estado === 'presente' ? 'success' : ($asistencia->estado === 'ausente' ? 'danger' : 'warning')) }}">
                                                                {{ ucfirst($asistencia->estado) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <!-- Primera fila de iconos -->
                                                        <div class="d-flex align-items-center gap-1">
                                                            <!-- Ver -->
                                                            <a href="{{ route('asambleas.asistencias.show', $asistencia) }}" 
                                                               class="btn btn-soft-primary btn-sm" 
                                                               title="Ver detalles"
                                                               data-bs-toggle="tooltip">
                                                                <i class="ri-eye-line fs-4"></i>
                                                            </a>
                                                            
                                                            <!-- Editar -->
                                                            <a href="{{ route('asambleas.asistencias.edit', $asistencia) }}" 
                                                               class="btn btn-soft-warning btn-sm" 
                                                               title="Editar"
                                                               data-bs-toggle="tooltip">
                                                                <i class="ri-edit-line fs-4"></i>
                                                            </a>
                                                            
                                                            <!-- Eliminar -->
                                                            <button type="button" 
                                                                    class="btn btn-soft-danger btn-sm" 
                                                                    title="Eliminar"
                                                                    data-bs-toggle="tooltip"
                                                                    onclick="deleteAsistencia({{ $asistencia->id }}, '{{ $asistencia->miembro->nombre }} {{ $asistencia->miembro->apellido }}')">
                                                                <i class="ri-delete-bin-line fs-4"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Segunda fila de iconos -->
                                                        <div class="d-flex align-items-center gap-1">
                                                            @if($asistencia->estado === 'confirmada')
                                                                <button type="button" 
                                                                        class="btn btn-soft-success btn-sm" 
                                                                        title="Marcar como Presente"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="marcarPresente({{ $asistencia->id }})">
                                                                    <i class="ri-user-check-line fs-4"></i>
                                                                </button>
                                                            @elseif($asistencia->estado === 'presente')
                                                                <button type="button" 
                                                                        class="btn btn-soft-danger btn-sm" 
                                                                        title="Marcar como Ausente"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="marcarAusente({{ $asistencia->id }})">
                                                                    <i class="ri-user-unfollow-line fs-4"></i>
                                                                </button>
                                                            @endif
                                                            
                                                            @if($asistencia->estado !== 'tardanza')
                                                                <button type="button" 
                                                                        class="btn btn-soft-warning btn-sm" 
                                                                        title="Marcar Tardanza"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="marcarTardanza({{ $asistencia->id }})">
                                                                    <i class="ri-time-line fs-4"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="ri-user-line fs-48 mb-3 d-block"></i>
                                                        <h5>No hay asistencias registradas</h5>
                                                        <p class="mb-0">No se encontraron asistencias en el sistema.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación Mejorada CLDCI -->
                @if($asistencias->hasPages() || $asistencias->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $asistencias->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $asistencias->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $asistencias->total() }}</strong> 
                                    asistencias
                                </span>
                            </div>
                        </div>
                        
                        <!-- Controles de paginación -->
                        <div class="col-md-6">
                            <div class="cldci-pagination">
                                <!-- Botón Primera Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina(1)" 
                                        title="Primera página" 
                                        {{ $asistencias->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $asistencias->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $asistencias->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $asistencias->currentPage();
                                        $lastPage = $asistencias->lastPage();
                                        $numPagesToShow = 5; // Número de páginas a mostrar directamente
                                        $startPage = max(1, $currentPage - floor($numPagesToShow / 2));
                                        $endPage = min($lastPage, $startPage + $numPagesToShow - 1);

                                        // Ajustar si estamos cerca del inicio o final
                                        if ($endPage - $startPage < $numPagesToShow - 1) {
                                            if ($startPage == 1) {
                                                $endPage = min($lastPage, $startPage + $numPagesToShow - 1);
                                            } else {
                                                $startPage = max(1, $endPage - $numPagesToShow + 1);
                                            }
                                        }
                                    @endphp

                                    @if($startPage > 1)
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPagina(1)">1</button>
                                        @if($startPage > 2)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                    @endif

                                    @for($i = $startPage; $i <= $endPage; $i++)
                                        <button type="button" 
                                                class="pagination-btn pagination-number {{ $i == $currentPage ? 'active' : '' }}" 
                                                onclick="cambiarPagina({{ $i }})">
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPagina({{ $lastPage }})">{{ $lastPage }}</button>
                                    @endif
                                </div>
                                
                                <!-- Botón Página Siguiente -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $asistencias->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $asistencias->currentPage() >= $asistencias->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $asistencias->lastPage() }})" 
                                        title="Última página" 
                                        {{ $asistencias->currentPage() >= $asistencias->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($asistencias->currentPage() / $asistencias->lastPage()) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $asistencias->currentPage() }} de {{ $asistencias->lastPage() }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Funciones para sorting y paginación
function changePageSize(size) {
    const url = new URL(window.location);
    if (size === 'all') {
        url.searchParams.delete('per_page');
    } else {
        url.searchParams.set('per_page', size);
    }
    window.location.href = url.toString();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.asistencia-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.asistencia-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCount = document.getElementById('selectedCount');
    
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).count();
    
    if (checkedCount > 0) {
        bulkActionsDropdown.style.display = 'block';
        selectedCount.textContent = checkedCount;
    } else {
        bulkActionsDropdown.style.display = 'none';
    }
    
    // Actualizar estado del checkbox "Seleccionar todo"
    if (checkedCount === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedCount === checkboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
}

function bulkAction(action) {
    const selectedIds = getSelectedIds();
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos una asistencia.');
        return;
    }
    
    switch(action) {
        case 'export':
            exportSelectedAsistencias(selectedIds);
            break;
        case 'print':
            printSelectedAsistencias(selectedIds);
            break;
        case 'delete':
            deleteSelectedAsistencias(selectedIds);
            break;
    }
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.asistencia-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function exportSelectedAsistencias(ids) {
    // Implementar exportación
    console.log('Exportando asistencias:', ids);
}

function printSelectedAsistencias(ids) {
    // Implementar impresión
    console.log('Imprimiendo asistencias:', ids);
}

function deleteSelectedAsistencias(ids) {
    if (confirm(`¿Está seguro de eliminar ${ids.length} asistencia(s) seleccionada(s)?`)) {
        // Implementar eliminación masiva
        console.log('Eliminando asistencias:', ids);
    }
}

function deleteAsistencia(id, nombre) {
    if (confirm(`¿Está seguro de eliminar la asistencia de "${nombre}"?`)) {
        fetch(`/asambleas/asistencias/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast(`Asistencia de "${nombre}" eliminada exitosamente`);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al eliminar la asistencia');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al eliminar la asistencia');
        });
    }
}

function marcarPresente(id) {
    if (confirm('¿Está seguro de marcar como presente?')) {
        fetch(`/asambleas/asistencias/${id}/presente`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast('Miembro marcado como presente');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al marcar como presente');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al marcar como presente');
        });
    }
}

function marcarAusente(id) {
    if (confirm('¿Está seguro de marcar como ausente?')) {
        fetch(`/asambleas/asistencias/${id}/ausente`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast('Miembro marcado como ausente');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al marcar como ausente');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al marcar como ausente');
        });
    }
}

function marcarTardanza(id) {
    if (confirm('¿Está seguro de marcar con tardanza?')) {
        fetch(`/asambleas/asistencias/${id}/tardanza`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast('Miembro marcado con tardanza');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al marcar con tardanza');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al marcar con tardanza');
        });
    }
}

function exportarAsistencias() {
    // Implementar exportación
    console.log('Exportando todas las asistencias');
}

function imprimirLista() {
    // Implementar impresión
    console.log('Imprimiendo lista de asistencias');
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

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('asistencia-filters-form');
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
});
</script>
@endsection

