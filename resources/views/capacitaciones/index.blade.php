@extends('partials.layouts.master')

@section('title', 'Catálogo | CLDCI')
@section('title-sub', 'Gestión de Capacitaciones')
@section('pagetitle', 'Catálogo')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<style>
    /* ==========================================
       PAGINACIÓN CLDCI - DISEÑO MEJORADO
       ========================================== */
    
    .cldci-pagination-container {
        background: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }
    
    .pagination-info {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    
    .pagination-info strong {
        color: #495057;
        font-weight: 600;
    }
    
    .cldci-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #6c757d;
        text-decoration: none;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fa;
        color: #adb5bd;
    }
    
    .pagination-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }
    
    .pagination-btn.active:hover {
        background: #0b5ed7;
        border-color: #0b5ed7;
    }
    
    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin: 0 0.5rem;
    }
    
    .pagination-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .pagination-progress {
        margin-top: 1rem;
    }
    
    .pagination-progress .progress {
        border-radius: 2px;
        background: #e9ecef;
    }
    
    .pagination-progress .progress-bar {
        border-radius: 2px;
        transition: width 0.3s ease;
    }
    
    /* Animaciones */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .ri-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Responsive para paginación */
    @media (max-width: 768px) {
        .cldci-pagination-container {
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .cldci-pagination {
            justify-content: center;
            gap: 0.25rem;
        }
        
        .pagination-numbers {
            margin: 0 0.25rem;
            gap: 0.125rem;
        }
        
        .pagination-btn {
            min-width: 2.25rem;
            height: 2.25rem;
            font-size: 0.8rem;
        }
        
        .pagination-info {
            font-size: 0.8rem;
            text-align: center;
            margin-bottom: 1rem;
        }
    }
    
    /* Responsive para columna de acciones */
    @media (max-width: 1200px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 160px !important;
            width: 160px !important;
            max-width: 160px !important;
        }
        
        .capacitaciones-table .d-flex.flex-column.gap-1 {
            min-width: 140px !important;
            width: 140px !important;
        }
    }
    
    @media (max-width: 992px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 140px !important;
            width: 140px !important;
            max-width: 140px !important;
        }
        
        .capacitaciones-table .d-flex.flex-column.gap-1 {
            min-width: 120px !important;
            width: 120px !important;
        }
        
        .table-responsive .btn-soft-primary,
        .table-responsive .btn-soft-info,
        .table-responsive .btn-soft-success,
        .table-responsive .btn-soft-warning,
        .table-responsive .btn-soft-danger {
            min-width: 1.75rem !important;
            height: 1.75rem !important;
            padding: 0.25rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 120px !important;
            width: 120px !important;
            max-width: 120px !important;
        }
        
        .capacitaciones-table .d-flex.flex-column.gap-1 {
            min-width: 100px !important;
            width: 100px !important;
        }
        
        .table-responsive .btn-soft-primary,
        .table-responsive .btn-soft-info,
        .table-responsive .btn-soft-success,
        .table-responsive .btn-soft-warning,
        .table-responsive .btn-soft-danger {
            min-width: 1.5rem !important;
            height: 1.5rem !important;
            padding: 0.2rem !important;
        }
    }

    /* ==========================================
       ICONOS DE ACCIONES - DISEÑO COHERENTE MEJORADO
       ========================================== */
    
    /* Ajustes específicos para la columna de acciones */
    .capacitaciones-table th:last-child,
    .capacitaciones-table td:last-child {
        min-width: 180px !important;
        width: 180px !important;
        max-width: 180px !important;
        white-space: nowrap !important;
    }
    
    /* Contenedor de acciones con ancho fijo */
    .capacitaciones-table .d-flex.flex-column.gap-1 {
        min-width: 160px !important;
        width: 160px !important;
    }
    
    /* Estilos para botones de acción con iconos */
    .table-responsive .btn-soft-primary,
    .table-responsive .btn-soft-info,
    .table-responsive .btn-soft-success,
    .table-responsive .btn-soft-warning,
    .table-responsive .btn-soft-danger {
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
    
    /* Iconos dentro de los botones */
    .table-responsive .btn i {
        font-size: 0.875rem !important;
        line-height: 1 !important;
    }
    
    /* Contenedor de acciones */
    .table-responsive .d-flex.flex-column.gap-1 {
        width: max-content !important;
        gap: 0.5rem !important;
    }
    
    /* Espaciado entre iconos en cada fila */
    .table-responsive .d-flex.align-items-center.gap-1 {
        gap: 0.375rem !important;
    }
    
    /* Responsive para móviles */
    @media (max-width: 768px) {
        .table-responsive .btn-soft-primary,
        .table-responsive .btn-soft-info,
        .table-responsive .btn-soft-success,
        .table-responsive .btn-soft-warning,
        .table-responsive .btn-soft-danger {
            min-width: 1.75rem !important;
            height: 1.75rem !important;
            padding: 0.25rem !important;
        }
        
        .table-responsive .btn i {
            font-size: 0.75rem !important;
        }
        
        .table-responsive .d-flex.gap-1 {
            gap: 0.25rem !important;
        }
    }

    /* ==========================================
       COLUMNA DE ACCIONES - ESTILOS CONSOLIDADOS
       ========================================== */
    
    /* Ancho optimizado para la columna de acciones */
    .capacitaciones-table th:last-child,
    .capacitaciones-table td:last-child {
        min-width: 140px !important;
        width: 140px !important;
        max-width: 140px !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
        padding: 0.5rem !important;
        text-align: center !important;
    }
    
    /* Contenedor de acciones compacto */
    .capacitaciones-table td:last-child .d-flex.flex-column.gap-1 {
        min-width: 120px !important;
        width: 120px !important;
        gap: 0.375rem !important;
        align-items: center !important;
    }
    
    /* Asegurar que la tabla tenga suficiente espacio */
    .table-responsive {
        min-width: 100% !important;
        width: 100% !important;
        overflow-x: auto !important;
    }
    
    /* Estilos optimizados para los botones de acción */
    .capacitaciones-table .btn-soft-primary,
    .capacitaciones-table .btn-soft-success,
    .capacitaciones-table .btn-soft-warning,
    .capacitaciones-table .btn-soft-danger {
        min-width: 2rem !important;
        height: 2rem !important;
        padding: 0.375rem !important;
        margin: 0.125rem !important;
        flex-shrink: 0 !important;
        border-radius: 0.375rem !important;
        transition: all 0.2s ease !important;
    }
    
    /* Iconos dentro de los botones */
    .capacitaciones-table .btn i {
        font-size: 0.875rem !important;
        line-height: 1 !important;
    }
    
    /* Espaciado entre filas de iconos */
    .capacitaciones-table .d-flex.align-items-center.gap-1 {
        gap: 0.375rem !important;
        justify-content: center !important;
    }
    
    /* Responsive para columna de acciones */
    @media (max-width: 1200px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 130px !important;
            width: 130px !important;
            max-width: 130px !important;
        }
        
        .capacitaciones-table td:last-child .d-flex.flex-column.gap-1 {
            min-width: 110px !important;
            width: 110px !important;
        }
    }
    
    @media (max-width: 992px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 120px !important;
            width: 120px !important;
            max-width: 120px !important;
        }
        
        .capacitaciones-table td:last-child .d-flex.flex-column.gap-1 {
            min-width: 100px !important;
            width: 100px !important;
        }
        
        .capacitaciones-table .btn-soft-primary,
        .capacitaciones-table .btn-soft-success,
        .capacitaciones-table .btn-soft-warning,
        .capacitaciones-table .btn-soft-danger {
            min-width: 1.75rem !important;
            height: 1.75rem !important;
            padding: 0.25rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .capacitaciones-table th:last-child,
        .capacitaciones-table td:last-child {
            min-width: 110px !important;
            width: 110px !important;
            max-width: 110px !important;
        }
        
        .capacitaciones-table td:last-child .d-flex.flex-column.gap-1 {
            min-width: 90px !important;
            width: 90px !important;
        }
        
        .capacitaciones-table .btn-soft-primary,
        .capacitaciones-table .btn-soft-success,
        .capacitaciones-table .btn-soft-warning,
        .capacitaciones-table .btn-soft-danger {
            min-width: 1.5rem !important;
            height: 1.5rem !important;
            padding: 0.2rem !important;
        }
        
        .capacitaciones-table .btn i {
            font-size: 0.75rem !important;
        }
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
            height: 28px !important;
            font-size: 0.7rem !important;
            padding: 0.2rem 0.4rem !important;
        }
        
        .per-page-selector::after {
            right: 0.25rem !important;
            font-size: 0.7rem !important;
        }
        
        .d-flex.justify-content-between.align-items-center {
            flex-direction: column !important;
            gap: 0.75rem !important;
            text-align: center !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Estadísticas -->
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xxl col-sm-6">
                <div class="card border-0 bg-primary-subtle">
                    <div class="card-body text-center">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="ri-graduation-cap-line text-white fs-4"></i>
                        </div>
                        <h6 class="mb-1">{{ $estadisticas['total_capacitaciones'] }}</h6>
                        <p class="text-muted mb-0 small">Total Cursos</p>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 bg-success-subtle">
                    <div class="card-body text-center">
                        <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="ri-check-line text-white fs-4"></i>
                        </div>
                        <h6 class="mb-1">{{ $estadisticas['activas'] }}</h6>
                        <p class="text-muted mb-0 small">Activas</p>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 bg-warning-subtle">
                    <div class="card-body text-center">
                        <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="ri-pause-line text-white fs-4"></i>
                        </div>
                        <h6 class="mb-1">{{ $estadisticas['inactivas'] }}</h6>
                        <p class="text-muted mb-0 small">Inactivas</p>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 bg-info-subtle">
                    <div class="card-body text-center">
                        <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="ri-calendar-line text-white fs-4"></i>
                        </div>
                        <h6 class="mb-1">{{ $estadisticas['proximas'] }}</h6>
                        <p class="text-muted mb-0 small">Próximas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Globales -->
    <x-global-filter
        title="Filtros de Búsqueda"
        description="Refine los resultados utilizando los filtros disponibles"
        icon="ri-search-line"
        form-id="capacitaciones-filters-form"
        form-action="{{ route('capacitaciones.index') }}"
        clear-url="{{ route('capacitaciones.index') }}"
        submit-label="Buscar"
        clear-label="Limpiar"
        variant="default"
        :filters="[
            [
                'name' => 'buscar',
                'label' => 'Buscar',
                'type' => 'text',
                'placeholder' => 'Título, instructor o descripción',
                'col' => 'col-md-4'
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
                'name' => 'activo',
                'label' => 'Estado',
                'type' => 'select',
                'placeholder' => 'Todos los estados',
                'col' => 'col-md-3',
                'options' => [
                    '1' => 'Activa',
                    '0' => 'Inactiva'
                ]
            ],
            [
                'name' => 'fecha_desde',
                'label' => 'Fecha Desde',
                'type' => 'date',
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
                        Vista
                    </h6>
                </div>
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

    <!-- Tabla de Capacitaciones -->
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('capacitaciones.create') }}" class="btn btn-agregar">
                            <i class="ri-graduation-cap-line"></i>
                            <span>Agregar Curso</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-graduation-cap-line"></i>
                            Catálogo de Cursos
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Gestión completa de cursos de capacitación
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
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarCapacitaciones()">
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
                    <table class="table table-hover text-nowrap capacitaciones-table">
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
                                    Curso <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_inicio">
                                    Fecha Inicio <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="costo">
                                    Costo <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="inscripciones">
                                    Inscritos <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="activo">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="text-center" style="min-width: 140px; width: 140px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($capacitaciones as $capacitacion)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input capacitacion-checkbox" type="checkbox" 
                                               value="{{ $capacitacion->id }}" 
                                               id="capacitacion_{{ $capacitacion->id }}"
                                               onchange="updateSelectAllState()">
                                        <label class="form-check-label" for="capacitacion_{{ $capacitacion->id }}">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                    <i class="ri-graduation-cap-line fs-12"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0">{{ $capacitacion->titulo }}</h6>
                                                <p class="text-muted mb-0 fs-12">
                                                    <i class="ri-file-text-line me-1"></i>{{ Str::limit($capacitacion->descripcion, 50) }}
                                                </p>
                                                <p class="text-muted mb-0 fs-12">
                                                    <i class="ri-user-line me-1"></i>{{ $capacitacion->instructor }}
                                                </p>
                                                <p class="text-muted mb-0 fs-12">
                                                    <i class="ri-{{ $capacitacion->modalidad === 'presencial' ? 'building' : ($capacitacion->modalidad === 'virtual' ? 'computer' : 'mix') }}-line me-1"></i>
                                                    Modalidad - {{ ucfirst($capacitacion->modalidad) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                <td>
                                    <span class="fw-semibold">{{ $capacitacion->fecha_inicio->format('d/m/Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $capacitacion->fecha_inicio->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold">RD$ {{ number_format($capacitacion->costo, 0) }}</span>
                                    @if($capacitacion->cupo_maximo)
                                    <br>
                                    <small class="text-muted">Cupo: {{ $capacitacion->cupo_maximo }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                        </div>
                                        <span class="fw-semibold">0</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center estado-container">
                                        <div class="flex-shrink-0 me-2">
                                            @if($capacitacion->activo)
                                                <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-check-line text-success fs-10"></i>
                                                </div>
                                            @else
                                                <div class="avatar-xs bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-pause-line text-danger fs-10"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="badge bg-{{ $capacitacion->activo ? 'success' : 'danger' }} bg-opacity-10 text-{{ $capacitacion->activo ? 'success' : 'danger' }} border border-{{ $capacitacion->activo ? 'success' : 'danger' }} border-opacity-25 fw-semibold">
                                                {{ $capacitacion->activo ? 'Activa' : 'Inactiva' }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $capacitacion->activo ? 'Curso disponible' : 'Curso no disponible' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" style="min-width: 140px; width: 140px;">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <!-- Primera fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Ver Detalles -->
                                            <a href="{{ route('capacitaciones.show', $capacitacion) }}" 
                                               class="btn btn-soft-primary btn-sm" 
                                               title="Ver Detalles"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            
                                            <!-- Enviar Email -->
                                            <a href="mailto:{{ $capacitacion->instructor }}" 
                                               class="btn btn-soft-success btn-sm" 
                                               title="Enviar Email"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-mail-line"></i>
                                            </a>
                                        </div>
                                        
                                        <!-- Segunda fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Editar -->
                                            <a href="{{ route('capacitaciones.edit', $capacitacion) }}" 
                                               class="btn btn-soft-warning btn-sm" 
                                               title="Editar"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            
                                            <!-- Eliminar -->
                                            <button type="button" 
                                                    class="btn btn-soft-danger btn-sm" 
                                                    title="Eliminar"
                                                    data-bs-toggle="tooltip"
                                                    onclick="deleteCapacitacion({{ $capacitacion->id }}, '{{ $capacitacion->titulo }}')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-graduation-cap-line fs-1 mb-3 d-block"></i>
                                        <h5>No hay capacitaciones registradas</h5>
                                        <p>Comience creando su primera capacitación.</p>
                                        <a href="{{ route('capacitaciones.create') }}" class="btn btn-primary">
                                            <i class="ri-add-line me-1"></i> Crear Primera Capacitación
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Paginación Mejorada CLDCI -->
                @if($capacitaciones->hasPages() || $capacitaciones->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $capacitaciones->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $capacitaciones->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $capacitaciones->total() }}</strong> 
                                    capacitaciones
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
                                        {{ $capacitaciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $capacitaciones->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $capacitaciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $capacitaciones->currentPage();
                                        $lastPage = $capacitaciones->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                        
                                        // Ajustar si estamos cerca del inicio o final
                                        if ($endPage - $startPage < 4) {
                                            if ($startPage == 1) {
                                                $endPage = min($lastPage, $startPage + 4);
                                            } else {
                                                $startPage = max(1, $endPage - 4);
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
                                        onclick="cambiarPagina({{ $capacitaciones->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $capacitaciones->currentPage() >= $capacitaciones->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $capacitaciones->lastPage() }})" 
                                        title="Última página" 
                                        {{ $capacitaciones->currentPage() >= $capacitaciones->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ $capacitaciones->total() > 0 ? ($capacitaciones->currentPage() / $capacitaciones->lastPage()) * 100 : 0 }}%"
                                 role="progressbar" 
                                 aria-valuenow="{{ $capacitaciones->currentPage() }}" 
                                 aria-valuemin="1" 
                                 aria-valuemax="{{ $capacitaciones->lastPage() }}">
                            </div>
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
// Funciones de eliminación
function deleteCapacitacion(capacitacionId, capacitacionTitulo) {
    if (confirm(`¿Está seguro de eliminar la capacitación "${capacitacionTitulo}"?`)) {
        fetch(`/capacitaciones/${capacitacionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                showSuccessToast(`Capacitación "${capacitacionTitulo}" eliminada exitosamente`);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al eliminar la capacitación');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al eliminar la capacitación');
        });
    }
}

// Funciones de exportación e impresión
function exportarCapacitaciones() {
    showSuccessToast('Exportando capacitaciones...');
    // Implementar lógica de exportación
}

function imprimirLista() {
    showSuccessToast('Preparando lista para impresión...');
    // Implementar lógica de impresión
}

// Funciones de acciones masivas
function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.capacitacion-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        showErrorToast('Seleccione al menos una capacitación');
        return;
    }
    
    switch(action) {
        case 'export':
            showSuccessToast(`Exportando ${selectedIds.length} capacitaciones seleccionadas...`);
            break;
        case 'print':
            showSuccessToast(`Imprimiendo ${selectedIds.length} capacitaciones seleccionadas...`);
            break;
        case 'email':
            showSuccessToast(`Enviando email a ${selectedIds.length} capacitaciones seleccionadas...`);
            break;
        case 'status':
            showSuccessToast(`Cambiando estado de ${selectedIds.length} capacitaciones seleccionadas...`);
            break;
        case 'delete':
            if (confirm(`¿Está seguro de eliminar ${selectedIds.length} capacitaciones seleccionadas?`)) {
                showSuccessToast(`Eliminando ${selectedIds.length} capacitaciones seleccionadas...`);
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

// Función para cambiar de página
function cambiarPagina(page) {
    if (page < 1) return;
    
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    
    // Mostrar indicador de carga
    showPaginationLoading();
    
    window.location.href = url.toString();
}

// Función para mostrar loading en paginación
function showPaginationLoading() {
    // Remover spinner existente si existe
    const existingSpinner = document.querySelector('.pagination-spinner');
    if (existingSpinner) {
        existingSpinner.remove();
    }
    
    // Mostrar indicador de carga
    const paginationContainer = document.querySelector('.cldci-pagination-container');
    if (paginationContainer) {
        paginationContainer.style.opacity = '0.6';
        paginationContainer.style.pointerEvents = 'none';
    }
    
    // Agregar efecto de loading a los botones
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    paginationBtns.forEach(btn => {
        btn.style.opacity = '0.5';
        btn.disabled = true;
    });
    
    // Crear y mostrar spinner
    const spinner = document.createElement('div');
    spinner.className = 'pagination-spinner';
    spinner.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Cargando...';
    spinner.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        font-size: 0.875rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    `;
    
    if (paginationContainer) {
        paginationContainer.style.position = 'relative';
        paginationContainer.appendChild(spinner);
    }
}

// Funcionalidad de selección múltiple
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const capacitacionCheckboxes = document.querySelectorAll('.capacitacion-checkbox');
    
    capacitacionCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const capacitacionCheckboxes = document.querySelectorAll('.capacitacion-checkbox');
    const checkedBoxes = document.querySelectorAll('.capacitacion-checkbox:checked');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCount = document.getElementById('selectedCount');
    
    selectAllCheckbox.checked = checkedBoxes.length === capacitacionCheckboxes.length;
    selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < capacitacionCheckboxes.length;
    
    // Mostrar/ocultar acciones masivas
    if (checkedBoxes.length > 0) {
        bulkActionsDropdown.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActionsDropdown.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const capacitacionCheckboxes = document.querySelectorAll('.capacitacion-checkbox');
    
    selectAllCheckbox.addEventListener('change', toggleSelectAll);
    
    capacitacionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });
});
</script>
@endsection
