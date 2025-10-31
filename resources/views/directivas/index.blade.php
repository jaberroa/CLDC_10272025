@extends('partials.layouts.master')

@section('title', 'Directiva | CLDCI')
@section('title-sub', 'Gestión de Directiva')
@section('pagetitle', 'Directiva')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<style>
    /* ==========================================
       ICONOS DE ACCIONES - DISEÑO COHERENTE MEJORADO
       ========================================== */
    
    /* Asegurar que todos los botones de acción tengan el mismo ancho */
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
        width: 2rem !important;
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
        white-space: nowrap !important;
    }
    
    /* Asegurar que la tabla tenga suficiente espacio */
    .table-responsive {
        overflow-x: auto !important;
        min-width: 100% !important;
    }
    
    /* Asegurar que el contenedor de la tabla no limite el ancho */
    .table-responsive .table {
        width: 100% !important;
        table-layout: auto;
    }
    
    /* Ajustar anchos específicos de columnas */
    .table-responsive .table th:nth-child(1),
    .table-responsive .table td:nth-child(1) {
        width: 50px;
        min-width: 50px;
    }
    
    .table-responsive .table th:nth-child(2),
    .table-responsive .table td:nth-child(2) {
        width: 25%;
        min-width: 200px;
    }
    
    .table-responsive .table th:nth-child(3),
    .table-responsive .table td:nth-child(3) {
        width: 15%;
        min-width: 120px;
    }
    
    .table-responsive .table th:nth-child(4),
    .table-responsive .table td:nth-child(4) {
        width: 12%;
        min-width: 100px;
    }
    
    .table-responsive .table th:nth-child(5),
    .table-responsive .table td:nth-child(5) {
        width: 12%;
        min-width: 100px;
    }
    
    .table-responsive .table th:nth-child(6),
    .table-responsive .table td:nth-child(6) {
        width: 12%;
        min-width: 100px;
    }
    
    .table-responsive .table th:nth-child(7),
    .table-responsive .table td:nth-child(7) {
        width: 12%;
        min-width: 100px;
    }
    
    .table-responsive .table th:nth-child(8),
    .table-responsive .table td:nth-child(8) {
        width: 10%;
        min-width: 150px;
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
            gap: 0.125rem !important;
        }
        
        .table-responsive .d-flex.flex-column.gap-1 {
            gap: 0.125rem !important;
        }
        
        .table-responsive th:last-child,
        .table-responsive td:last-child {
            min-width: 120px !important;
            width: 120px !important;
        }
    }

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
        flex-wrap: wrap;
    }
    
    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin: 0 0.5rem;
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
        color: #495057;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #212529;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .pagination-btn:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        border-color: #0d6efd;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fa;
        color: #6c757d;
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
    }
    
    .pagination-btn.active:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    }
    
    .pagination-btn-nav {
        min-width: 2.5rem;
        background: #f8f9fa;
        border-color: #e9ecef;
    }
    
    .pagination-btn-nav:hover:not(:disabled) {
        background: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination-btn-nav i {
        font-size: 1rem;
    }
    
    .pagination-number {
        min-width: 2.5rem;
        font-weight: 500;
    }
    
    .pagination-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        color: #6c757d;
        font-weight: 500;
        user-select: none;
    }
    
    .pagination-progress {
        margin-top: 1rem;
    }
    
    .pagination-progress .progress {
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .pagination-progress .progress-bar {
        background: linear-gradient(90deg, #0d6efd 0%, #0b5ed7 100%);
        border-radius: 2px;
        transition: width 0.3s ease;
    }
    
    /* Animaciones para la paginación */
    .pagination-btn {
        position: relative;
        overflow: hidden;
    }
    
    .pagination-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }
    
    .pagination-btn:hover::before {
        left: 100%;
    }
    
    /* Spinner de paginación */
    .pagination-spinner {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
        to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
    
    .ri-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
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
        
        .pagination-btn-nav {
            min-width: 2.25rem;
        }
        
        .pagination-info {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .pagination-progress {
            margin-top: 0.75rem;
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
    
    @media (max-width: 576px) {
        .cldci-pagination {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .pagination-numbers {
            order: -1;
            margin: 0;
        }
        
        .pagination-btn-nav {
            min-width: 2rem;
            height: 2rem;
        }
        
        .pagination-btn {
            min-width: 2rem;
            height: 2rem;
            font-size: 0.75rem;
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
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-group-line text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Total Directivas</h6>
                                <h4 class="mb-0 fw-bold">{{ $estadisticas['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-check-line text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Activas</h6>
                                <h4 class="mb-0 fw-bold">{{ $estadisticas['activos'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-info-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-time-line text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Vigentes</h6>
                                <h4 class="mb-0 fw-bold">{{ $estadisticas['vigentes'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-alarm-warning-line text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-0">Vencidas</h6>
                                <h4 class="mb-0 fw-bold">{{ $estadisticas['vencidos'] }}</h4>
                            </div>
                        </div>
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
        form-id="directivas-filters-form"
        form-action="{{ route('directivas.index') }}"
        clear-url="{{ route('directivas.index') }}"
        submit-label="Buscar"
        clear-label="Limpiar"
        variant="default"
        :filters="[
            [
                'name' => 'buscar',
                'label' => 'Buscar',
                'type' => 'text',
                'placeholder' => 'Nombre, cédula o email',
                'col' => 'col-md-4'
            ],
            [
                'name' => 'estado',
                'label' => 'Estado',
                'type' => 'select',
                'placeholder' => 'Todos los estados',
                'col' => 'col-md-3',
                'options' => [
                    'activo' => 'Activo',
                    'inactivo' => 'Inactivo',
                    'suspendido' => 'Suspendido'
                ]
            ],
            [
                'name' => 'organo_id',
                'label' => 'Órgano',
                'type' => 'select',
                'placeholder' => 'Todos los órganos',
                'col' => 'col-md-3',
                'options' => $organos->pluck('nombre', 'id')->toArray()
            ],
            [
                'name' => 'cargo_id',
                'label' => 'Cargo',
                'type' => 'select',
                'placeholder' => 'Todos los cargos',
                'col' => 'col-md-2',
                'options' => $cargos->pluck('nombre', 'id')->toArray()
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

    <!-- Tabla de Directivas -->
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('directivas.create') }}" class="btn btn-agregar">
                            <i class="ri-group-add-line"></i>
                            <span>Agregar Directiva</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-group-line"></i>
                            Lista de Directivas
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Gestione y administre todas las directivas registradas en el sistema
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
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('delete')">
                                    <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionados
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                                    <i class="ri-check-line me-2"></i> Activar Seleccionados
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                                    <i class="ri-close-line me-2"></i> Desactivar Seleccionados
                                </a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="imprimirDirectivas()">
                            <i class="ri-printer-line me-1"></i> Imprimir
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarDirectivas()">
                            <i class="ri-download-line me-1"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body table-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap miembros-table">
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
                                <th class="sortable" data-sort="miembro_id">
                                    Directivo <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="organo_id">
                                    Órgano <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="periodo_directiva">
                                    Período <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="estado">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_inicio">
                                    Fecha Inicio <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_fin">
                                    Fecha Fin <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($directivas as $directiva)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input directiva-checkbox" type="checkbox" 
                                                   value="{{ $directiva->id }}" 
                                                   id="directiva_{{ $directiva->id }}"
                                                   onchange="updateSelectAllState()">
                                            <label class="form-check-label" for="directiva_{{ $directiva->id }}">
                                                <i class="ri-checkbox-line"></i>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                @if($directiva->miembro->foto_url)
                                                <img src="{{ asset('storage/' . $directiva->miembro->foto_url) }}" alt="" class="avatar-xs rounded-circle">
                                                @else
                                                <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                    <i class="ri-user-line fs-12"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0">{{ $directiva->miembro->nombre_completo }}</h6>
                                                <p class="text-muted mb-0 fs-12">
                                                    <i class="ri-briefcase-line me-1"></i>{{ $directiva->cargo->nombre }}
                                                </p>
                                                <p class="text-muted mb-0 fs-12">
                                                    <i class="ri-id-card-line me-1"></i>{{ $directiva->miembro->cedula }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $directiva->organo->nombre }}</span>
                                    </td>
                                    <td>
                                        @if($directiva->periodo_directiva)
                                            <span class="fw-semibold">{{ $directiva->periodo_directiva }}</span>
                                        @else
                                            <span class="text-muted">Sin período</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center estado-membresia-container">
                                            <div class="flex-shrink-0 me-2">
                                                @if($directiva->estado === 'activo')
                                                    <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-check-line text-success fs-10"></i>
                                                    </div>
                                                @elseif($directiva->estado === 'inactivo')
                                                    <div class="avatar-xs bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-close-line text-danger fs-10"></i>
                                                    </div>
                                                @else
                                                    <div class="avatar-xs bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-pause-line text-warning fs-10"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="badge bg-{{ $directiva->estado === 'activo' ? 'success' : ($directiva->estado === 'inactivo' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $directiva->estado === 'activo' ? 'success' : ($directiva->estado === 'inactivo' ? 'danger' : 'warning') }} border border-{{ $directiva->estado === 'activo' ? 'success' : ($directiva->estado === 'inactivo' ? 'danger' : 'warning') }} border-opacity-25 fw-semibold">
                                                    {{ ucfirst($directiva->estado) }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    @if($directiva->es_vigente)
                                                        Vigente
                                                    @elseif($directiva->es_vencido)
                                                        Vencido
                                                    @else
                                                        Estado de directiva
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $directiva->fecha_inicio->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        @if($directiva->fecha_fin)
                                            <span class="fw-semibold">{{ $directiva->fecha_fin->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-muted">Sin fecha de fin</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <!-- Primera fila de iconos -->
                                            <div class="d-flex align-items-center gap-1">
                                                <!-- Ver -->
                                                <a href="{{ route('directivas.show', $directiva) }}" 
                                                   class="btn btn-soft-info btn-sm" 
                                                   title="Ver detalles"
                                                   data-bs-toggle="tooltip">
                                                    <i class="ri-eye-line fs-4"></i>
                                                </a>
                                                
                                                <!-- Editar -->
                                                <a href="{{ route('directivas.edit', $directiva) }}" 
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
                                                        onclick="deleteDirectiva({{ $directiva->id }}, '{{ $directiva->miembro->nombre_completo }}')">
                                                    <i class="ri-delete-bin-line fs-4"></i>
                                                </button>
                                            </div>
                                            <!-- Segunda fila de iconos -->
                                            <div class="d-flex align-items-center gap-1">
                                                @if($directiva->estado === 'activo')
                                                    <button type="button" 
                                                            class="btn btn-soft-secondary btn-sm" 
                                                            title="Desactivar"
                                                            data-bs-toggle="tooltip"
                                                            onclick="deactivateDirectiva({{ $directiva->id }})">
                                                        <i class="ri-pause-line fs-4"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-soft-success btn-sm" 
                                                            title="Activar"
                                                            data-bs-toggle="tooltip"
                                                            onclick="activateDirectiva({{ $directiva->id }})">
                                                        <i class="ri-play-line fs-4"></i>
                                                    </button>
                                                @endif
                                                
                                                @if($directiva->estado !== 'suspendido')
                                                    <button type="button" 
                                                            class="btn btn-soft-warning btn-sm" 
                                                            title="Suspender"
                                                            data-bs-toggle="tooltip"
                                                            onclick="suspendDirectiva({{ $directiva->id }})">
                                                        <i class="ri-alarm-warning-line fs-4"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ri-group-line fs-1 d-block mb-2"></i>
                                            No se encontraron directivas
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación Mejorada CLDCI -->
                @if($directivas->hasPages() || $directivas->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $directivas->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $directivas->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $directivas->total() }}</strong> 
                                    directivas
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
                                        {{ $directivas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $directivas->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $directivas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $directivas->currentPage();
                                        $lastPage = $directivas->lastPage();
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
                                        onclick="cambiarPagina({{ $directivas->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $directivas->currentPage() >= $directivas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $directivas->lastPage() }})" 
                                        title="Última página" 
                                        {{ $directivas->currentPage() >= $directivas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($directivas->currentPage() / $directivas->lastPage()) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $directivas->currentPage() }} de {{ $directivas->lastPage() }}
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
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-submit del formulario de filtros cuando cambian los valores
    const filterForm = document.getElementById('directivas-filters-form');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
});

// Funciones para selección múltiple
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const directivaCheckboxes = document.querySelectorAll('.directiva-checkbox');
    
    directivaCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const directivaCheckboxes = document.querySelectorAll('.directiva-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectedCount = document.getElementById('selectedCount');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    
    const checkedBoxes = document.querySelectorAll('.directiva-checkbox:checked');
    const totalBoxes = directivaCheckboxes.length;
    
    // Actualizar contador
    selectedCount.textContent = checkedBoxes.length;
    
    // Mostrar/ocultar dropdown de acciones masivas
    if (checkedBoxes.length > 0) {
        bulkActionsDropdown.style.display = 'block';
    } else {
        bulkActionsDropdown.style.display = 'none';
    }
    
    // Actualizar estado del checkbox "Seleccionar todo"
    if (checkedBoxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === totalBoxes) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
}

// Funciones para acciones masivas
function bulkAction(action) {
    const selectedIds = getSelectedDirectivaIds();
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos una directiva.');
        return;
    }
    
    switch (action) {
        case 'delete':
            deleteSelectedDirectivas(selectedIds);
            break;
        case 'activate':
            activateSelectedDirectivas(selectedIds);
            break;
        case 'deactivate':
            deactivateSelectedDirectivas(selectedIds);
            break;
        default:
            console.log('Acción no implementada:', action);
    }
}

function getSelectedDirectivaIds() {
    const checkboxes = document.querySelectorAll('.directiva-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

function deleteSelectedDirectivas(selectedIds) {
    showDeleteConfirmation({
        title: `${selectedIds.length} directivas seleccionadas`,
        message: `Se eliminarán ${selectedIds.length} directivas seleccionadas permanentemente.`,
        type: 'múltiples directivas',
        onConfirm: () => {
            // Mostrar toast de carga
            showInfoToast(`Eliminando ${selectedIds.length} directivas...`, 'Procesando');
            
            // Preparar datos para envío
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            // Agregar IDs seleccionados
            selectedIds.forEach(id => {
                formData.append('ids[]', id);
            });
            
            // Realizar eliminación por AJAX
            fetch('{{ route("directivas.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Mostrar toast de éxito
                    showSuccessToast(`${selectedIds.length} directivas eliminadas exitosamente`);
                    
                    // Recargar la página después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error('Error al eliminar las directivas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorToast('Error al eliminar las directivas seleccionadas');
            });
        }
    });
}

function activateSelectedDirectivas(selectedIds) {
    console.log('Activando directivas:', selectedIds);
    alert(`Activando ${selectedIds.length} directivas seleccionadas`);
}

function deactivateSelectedDirectivas(selectedIds) {
    console.log('Desactivando directivas:', selectedIds);
    alert(`Desactivando ${selectedIds.length} directivas seleccionadas`);
}

// Funciones para acciones individuales
function deleteDirectiva(directivaId, directivaName) {
    showDeleteConfirmation({
        title: directivaName,
        type: 'directiva',
        onConfirm: () => {
            // Mostrar toast de carga
            showInfoToast('Eliminando directiva...', 'Procesando');
            
            // Realizar eliminación por AJAX
            fetch(`{{ url('directivas') }}/${directivaId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Mostrar toast de éxito
                    showSuccessToast(`Directiva "${directivaName}" eliminada exitosamente`);
                    
                    // Recargar la página después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error('Error al eliminar la directiva');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorToast('Error al eliminar la directiva');
            });
        }
    });
}

function activateDirectiva(directivaId) {
    console.log('Activando directiva:', directivaId);
    alert('Funcionalidad de activación no implementada aún');
}

function deactivateDirectiva(directivaId) {
    console.log('Desactivando directiva:', directivaId);
    alert('Funcionalidad de desactivación no implementada aún');
}

function suspendDirectiva(directivaId) {
    console.log('Suspendiendo directiva:', directivaId);
    alert('Funcionalidad de suspensión no implementada aún');
}

// Funciones para exportación e impresión
function exportarDirectivas() {
    const form = document.getElementById('directivas-filters-form');
    const action = form.action;
    form.action = '{{ route("directivas.exportar") }}';
    form.submit();
    form.action = action;
}

function imprimirDirectivas() {
    window.print();
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

function cambiarPagina(pagina) {
    if (pagina < 1 || pagina > {{ $directivas->lastPage() }}) return;
    
    // Limpiar spinner anterior si existe
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
        color: #495057;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    `;
    
    if (paginationContainer) {
        paginationContainer.style.position = 'relative';
        paginationContainer.appendChild(spinner);
    }
    
    // Navegar directamente sin delay
    const url = new URL(window.location);
    url.searchParams.set('page', pagina);
    window.location.href = url.toString();
}

// Sorting de columnas
document.addEventListener('DOMContentLoaded', function() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            const currentSort = new URLSearchParams(window.location.search).get('sort');
            const currentDirection = new URLSearchParams(window.location.search).get('direction');
            
            let newDirection = 'asc';
            if (currentSort === sortField && currentDirection === 'asc') {
                newDirection = 'desc';
            }
            
            const url = new URL(window.location);
            url.searchParams.set('sort', sortField);
            url.searchParams.set('direction', newDirection);
            window.location.href = url.toString();
        });
    });
});

// Mostrar toast de éxito si hay mensaje en la sesión
@if(session('success'))
    showSuccessToast('{{ session('success') }}');
@endif

@if(session('error'))
    showErrorToast('{{ session('error') }}');
@endif
</script>
@endsection