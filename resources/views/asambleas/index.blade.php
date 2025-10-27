@extends('partials.layouts.master')

@section('title', 'Lista de Asambleas | CLDCI')
@section('title-sub', 'Gestión de Asambleas')
@section('pagetitle', 'Lista de Asambleas')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/gridjs.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">

<style>
/* Estilos específicos para botones de acciones en asambleas */
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
                                    <i class="ri-calendar-line text-primary fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-primary">{{ $estadisticas['total'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Total Asambleas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-info-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-info-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-time-line text-info fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-info">{{ $estadisticas['programadas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Convocadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-warning-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-warning-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-play-line text-warning fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-warning">{{ $estadisticas['en_curso'] }}</h4>
                                <p class="text-muted mb-0 fs-12">En Proceso</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-success-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-success-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-check-line text-success fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-success">{{ $estadisticas['completadas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Finalizadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-danger-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-danger-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-close-line text-danger fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-danger">{{ $estadisticas['canceladas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Canceladas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-0 bg-purple-subtle">
                            <div class="card-body text-center">
                                <div class="avatar-sm bg-purple-subtle rounded-circle mx-auto mb-2">
                                    <i class="ri-calendar-event-line text-purple fs-18"></i>
                                </div>
                                <h4 class="mb-0 text-purple">{{ $estadisticas['proximas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Próximas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros de Búsqueda -->
                <x-global-filter
                    title="Filtros de Búsqueda"
                    description="Refine los resultados utilizando los filtros disponibles"
                    icon="ri-search-line"
                    form-id="asamblea-filters-form"
                    form-action="{{ route('asambleas.index') }}"
                    clear-url="{{ route('asambleas.index') }}"
                    submit-label="Buscar"
                    clear-label="Limpiar"
                    variant="default"
                    :filters="[
                        [
                            'name' => 'buscar',
                            'label' => 'Buscar',
                            'type' => 'text',
                            'placeholder' => 'Título, descripción o lugar',
                            'col' => 'col-md-3'
                        ],
                        [
                            'name' => 'tipo',
                            'label' => 'Tipo de Asamblea',
                            'type' => 'select',
                            'placeholder' => 'Todos los tipos',
                            'col' => 'col-md-2',
                            'options' => [
                                'ordinaria' => 'Ordinaria',
                                'extraordinaria' => 'Extraordinaria',
                                'especial' => 'Especial'
                            ]
                        ],
                        [
                            'name' => 'estado',
                            'label' => 'Estado',
                            'type' => 'select',
                            'placeholder' => 'Todos los estados',
                            'col' => 'col-md-2',
                            'options' => [
                                'convocada' => 'Convocada',
                                'en_proceso' => 'En Proceso',
                                'finalizada' => 'Finalizada',
                                'cancelada' => 'Cancelada'
                            ]
                        ],
                        [
                            'name' => 'organo_id',
                            'label' => 'Órgano',
                            'type' => 'select',
                            'placeholder' => 'Todos los órganos',
                            'col' => 'col-md-2',
                            'options' => $organos->pluck('nombre', 'id')->toArray()
                        ],
                        [
                            'name' => 'fecha_desde',
                            'label' => 'Desde',
                            'type' => 'date',
                            'placeholder' => 'Fecha desde',
                            'col' => 'col-md-1'
                        ],
                        [
                            'name' => 'fecha_hasta',
                            'label' => 'Hasta',
                            'type' => 'date',
                            'placeholder' => 'Fecha hasta',
                            'col' => 'col-md-1'
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
                                    Mostrando {{ $asambleas->count() }} de {{ $asambleas->total() }} asambleas
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

                <!-- Tabla de Asambleas -->
                <div class="col-xxl-12">
                    <div class="card shadow-sm">
                        <div class="card-header miembros-index-header">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <a href="{{ route('asambleas.create') }}" class="btn btn-agregar">
                                        <i class="ri-calendar-event-line"></i>
                                        <span>Agregar Asamblea</span>
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title">
                                        <i class="ri-calendar-line"></i>
                                        Lista de Asambleas
                                    </h4>
                                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                                        Gestión de asambleas y reuniones del sistema
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
                                    <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarAsambleas()">
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
                                            <th class="sortable" data-sort="titulo">
                                                Asamblea <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th class="sortable" data-sort="fecha_asamblea">
                                                Fecha <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th class="sortable" data-sort="estado">
                                                Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th class="sortable" data-sort="tipo">
                                                Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                                            </th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asambleas as $asamblea)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input asamblea-checkbox" type="checkbox" 
                                                               value="{{ $asamblea->id }}" 
                                                               id="asamblea_{{ $asamblea->id }}"
                                                               onchange="updateSelectAllState()">
                                                        <label class="form-check-label" for="asamblea_{{ $asamblea->id }}">
                                                            <i class="ri-checkbox-line"></i>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs bg-{{ $asamblea->tipo_asamblea_color }}-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="{{ $asamblea->tipo_asamblea_icon }} text-{{ $asamblea->tipo_asamblea_color }} fs-12"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $asamblea->titulo }}</h6>
                                                            @if($asamblea->descripcion)
                                                                <p class="text-muted mb-0 fs-12">{{ Str::limit($asamblea->descripcion, 50) }}</p>
                                                            @endif
                                                            @if($asamblea->lugar)
                                                                <p class="text-muted mb-0 fs-12">
                                                                    <i class="ri-map-pin-line me-1"></i>{{ $asamblea->lugar }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold">{{ $asamblea->fecha_asamblea->format('d/m/Y') }}</span>
                                                        <small class="text-muted">{{ $asamblea->fecha_asamblea->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center estado-membresia-container">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs bg-{{ $asamblea->estado_color }}-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="ri-{{ $asamblea->estado === 'convocada' ? 'time' : ($asamblea->estado === 'en_proceso' ? 'play' : ($asamblea->estado === 'finalizada' ? 'check' : 'close')) }}-line text-{{ $asamblea->estado_color }} fs-10"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <span class="badge bg-{{ $asamblea->estado_color }}-subtle text-{{ $asamblea->estado_color }}">
                                                                {{ ucfirst($asamblea->estado) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $asamblea->tipo_asamblea_color }}-subtle text-{{ $asamblea->tipo_asamblea_color }}">
                                                        {{ ucfirst($asamblea->tipo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <!-- Primera fila de iconos -->
                                                        <div class="d-flex align-items-center gap-1">
                                                            <!-- Ver -->
                                                            <a href="{{ route('asambleas.show', $asamblea) }}" 
                                                               class="btn btn-soft-primary btn-sm" 
                                                               title="Ver detalles"
                                                               data-bs-toggle="tooltip">
                                                                <i class="ri-eye-line fs-4"></i>
                                                            </a>
                                                            
                                                            <!-- Editar -->
                                                            <a href="{{ route('asambleas.edit', $asamblea) }}" 
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
                                                                    onclick="deleteAsamblea({{ $asamblea->id }}, '{{ $asamblea->titulo }}')">
                                                                <i class="ri-delete-bin-line fs-4"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Segunda fila de iconos -->
                                                        <div class="d-flex align-items-center gap-1">
                                                            @if($asamblea->estado === 'convocada')
                                                                <button type="button" 
                                                                        class="btn btn-soft-success btn-sm" 
                                                                        title="Iniciar"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="iniciarAsamblea({{ $asamblea->id }})">
                                                                    <i class="ri-play-line fs-4"></i>
                                                                </button>
                                                            @elseif($asamblea->estado === 'en_proceso')
                                                                <button type="button" 
                                                                        class="btn btn-soft-info btn-sm" 
                                                                        title="Completar"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="completarAsamblea({{ $asamblea->id }})">
                                                                    <i class="ri-check-line fs-4"></i>
                                                                </button>
                                                            @endif
                                                            
                                                            @if($asamblea->estado !== 'finalizada' && $asamblea->estado !== 'cancelada')
                                                                <button type="button" 
                                                                        class="btn btn-soft-secondary btn-sm" 
                                                                        title="Cancelar"
                                                                        data-bs-toggle="tooltip"
                                                                        onclick="cancelarAsamblea({{ $asamblea->id }})">
                                                                    <i class="ri-close-line fs-4"></i>
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
                                                        <i class="ri-calendar-line fs-48 mb-3 d-block"></i>
                                                        <h5>No hay asambleas registradas</h5>
                                                        <p class="mb-0">No se encontraron asambleas en el sistema.</p>
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
                @if($asambleas->hasPages() || $asambleas->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $asambleas->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $asambleas->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $asambleas->total() }}</strong> 
                                    asambleas
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
                                        {{ $asambleas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $asambleas->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $asambleas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $asambleas->currentPage();
                                        $lastPage = $asambleas->lastPage();
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
                                        onclick="cambiarPagina({{ $asambleas->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $asambleas->currentPage() >= $asambleas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $asambleas->lastPage() }})" 
                                        title="Última página" 
                                        {{ $asambleas->currentPage() >= $asambleas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($asambleas->currentPage() / $asambleas->lastPage()) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $asambleas->currentPage() }} de {{ $asambleas->lastPage() }}
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
    const checkboxes = document.querySelectorAll('.asamblea-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.asamblea-checkbox');
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
        alert('Por favor seleccione al menos una asamblea.');
        return;
    }
    
    switch(action) {
        case 'export':
            exportSelectedAsambleas(selectedIds);
            break;
        case 'print':
            printSelectedAsambleas(selectedIds);
            break;
        case 'delete':
            deleteSelectedAsambleas(selectedIds);
            break;
    }
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.asamblea-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function exportSelectedAsambleas(ids) {
    // Implementar exportación
    console.log('Exportando asambleas:', ids);
}

function printSelectedAsambleas(ids) {
    // Implementar impresión
    console.log('Imprimiendo asambleas:', ids);
}

function deleteSelectedAsambleas(ids) {
    if (confirm(`¿Está seguro de eliminar ${ids.length} asamblea(s) seleccionada(s)?`)) {
        // Implementar eliminación masiva
        console.log('Eliminando asambleas:', ids);
    }
}

function deleteAsamblea(id, titulo) {
    if (confirm(`¿Está seguro de eliminar la asamblea "${titulo}"?`)) {
        fetch(`/asambleas/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast(`Asamblea "${titulo}" eliminada exitosamente`);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al eliminar la asamblea');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al eliminar la asamblea');
        });
    }
}

function iniciarAsamblea(id) {
    if (confirm('¿Está seguro de iniciar esta asamblea?')) {
        // Implementar inicio de asamblea
        console.log('Iniciando asamblea:', id);
    }
}

function completarAsamblea(id) {
    if (confirm('¿Está seguro de completar esta asamblea?')) {
        // Implementar completar asamblea
        console.log('Completando asamblea:', id);
    }
}

function cancelarAsamblea(id) {
    if (confirm('¿Está seguro de cancelar esta asamblea?')) {
        // Implementar cancelar asamblea
        console.log('Cancelando asamblea:', id);
    }
}

function exportarAsambleas() {
    // Implementar exportación
    console.log('Exportando todas las asambleas');
}

function imprimirLista() {
    // Implementar impresión
    console.log('Imprimiendo lista de asambleas');
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
    const filterForm = document.getElementById('asamblea-filters-form');
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

