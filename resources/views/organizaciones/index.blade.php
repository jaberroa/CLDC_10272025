@extends('partials.layouts.master')

@section('title', 'Organizaciones | CLDCI')
@section('title-sub', 'Gestión de Organizaciones')
@section('pagetitle', 'Organizaciones')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/organizaciones-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/organizaciones-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/organizaciones-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/organizaciones/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/organizaciones/organizaciones-table.css') }}">
<style>
    /* ==========================================
       ESTILOS ESPECÍFICOS DE ORGANIZACIONES
       ========================================== */
    
    /* Colores de iconos - Respaldo */
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

    /* ==========================================
       TABLA RESPONSIVE - SCROLL HORIZONTAL
       ========================================== */
    
    /* Tabla responsive con scroll horizontal */
    .table-responsive {
        overflow-x: auto !important;
        min-width: 100% !important;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Asegurar que el contenedor de la tabla no limite el ancho */
    .table-responsive .table {
        width: 100% !important;
        margin-bottom: 0;
    }
    
    /* Asegurar que las columnas mantengan su ancho mínimo */
    .table-responsive .organizaciones-table {
        white-space: nowrap;
    }
    
    .table-responsive .organizaciones-table th,
    .table-responsive .organizaciones-table td {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Estadísticas -->
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-primary-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-primary d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-building-line"></i>
                            </div>
                            <div class="text-center flex-grow-1">
                                <span class="d-block fw-semibold mb-2 fs-5">Total Organizaciones</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-success-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-success d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-building-2-line"></i>
                            </div>
                            <div class="text-center flex-grow-1">
                                <span class="d-block fw-semibold mb-2 fs-5">Organizaciones Activas</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['activas']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-info-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-info d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-flag-line"></i>
                            </div>
                            <div class="text-center flex-grow-1">
                                <span class="d-block fw-semibold mb-2 fs-5">Nacionales</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['nacionales']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-warning-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-warning d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="text-center flex-grow-1">
                                <span class="d-block fw-semibold mb-2 fs-5">Seccionales</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['seccionales']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Globales -->
    <div class="col-xxl-12">
        <div class="card shadow-sm global-filter-container">
            <div class="card-header global-filter-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="global-filter-icon">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="global-filter-title">Filtros de Búsqueda</h5>
                        <p class="global-filter-description">Refine los resultados utilizando los filtros disponibles</p>
                    </div>
                </div>
            </div>
            <div class="card-body global-filter-body">
                <form method="GET" action="{{ route('organizaciones.index') }}" id="organizaciones-filters-form" class="global-filter-form">
                    <div class="global-filter-grid">
                        <div class="global-filter-field col-md-4">
                            <label for="buscar" class="global-filter-label">Buscar</label>
                            <input type="text" 
                                   class="global-filter-input" 
                                   id="buscar" 
                                   name="buscar" 
                                   value="{{ request('buscar') }}" 
                                   placeholder="Nombre, código o dirección">
                        </div>
                        <div class="global-filter-field col-md-3">
                            <label for="tipo" class="global-filter-label">Tipo de Organización</label>
                            <select class="global-filter-select" id="tipo" name="tipo">
                                <option value="">Todos los tipos</option>
                                <option value="nacional" {{ request('tipo') == 'nacional' ? 'selected' : '' }}>Nacional</option>
                                <option value="seccional" {{ request('tipo') == 'seccional' ? 'selected' : '' }}>Seccional</option>
                                <option value="seccional_internacional" {{ request('tipo') == 'seccional_internacional' ? 'selected' : '' }}>Seccional Internacional</option>
                                <option value="diaspora" {{ request('tipo') == 'diaspora' ? 'selected' : '' }}>Diáspora</option>
                            </select>
                        </div>
                        <div class="global-filter-field col-md-3">
                            <label for="estado" class="global-filter-label">Estado</label>
                            <select class="global-filter-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                                <option value="suspendida" {{ request('estado') == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                <option value="inactiva" {{ request('estado') == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>
                        <div class="global-filter-field col-md-2">
                            <label for="con_miembros" class="global-filter-label">Con Miembros</label>
                            <select class="global-filter-select" id="con_miembros" name="con_miembros">
                                <option value="">Todas</option>
                                <option value="si" {{ request('con_miembros') == 'si' ? 'selected' : '' }}>Sí</option>
                                <option value="no" {{ request('con_miembros') == 'no' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        
                        <div class="global-filter-actions">
                            <button type="submit" class="global-filter-btn global-filter-btn-primary">
                                <i class="ri-search-line"></i>
                                <span>Buscar</span>
                            </button>
                            <a href="{{ route('organizaciones.index') }}" class="global-filter-btn global-filter-btn-secondary">
                                <i class="ri-refresh-line"></i>
                                <span>Limpiar</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- Tabla de Organizaciones -->
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header organizaciones-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('organizaciones.create') }}" class="btn btn-agregar">
                            <i class="ri-building-add-line"></i>
                            <span>Agregar Organización</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-building-line"></i>
                            Lista de Organizaciones
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Gestione y administre todas las organizaciones registradas en el sistema
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
                                    <i class="ri-download-line me-2"></i> Exportar Seleccionadas
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('print')">
                                    <i class="ri-printer-line me-2"></i> Imprimir Seleccionadas
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('email')">
                                    <i class="ri-mail-line me-2"></i> Enviar Email
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('status')">
                                    <i class="ri-settings-line me-2"></i> Cambiar Estado
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                    <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionadas
                                </a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarOrganizaciones()">
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
                    <table class="table table-hover text-nowrap organizaciones-table">
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
                                <th class="sortable" data-sort="nombre">
                                    Organización <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="tipo">
                                    Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="estado">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="created_at">
                                    Fecha Registro <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="text-center" style="min-width: 140px; width: 140px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organizaciones as $organizacion)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input organizacion-checkbox" type="checkbox" 
                                               value="{{ $organizacion->id }}" 
                                               id="organizacion_{{ $organizacion->id }}"
                                               onchange="updateSelectAllState()">
                                        <label class="form-check-label" for="organizacion_{{ $organizacion->id }}">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            @if($organizacion->logo_url)
                                                <img src="{{ $organizacion->logo_url }}" 
                                                     alt="{{ $organizacion->nombre }}" 
                                                     class="rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="ri-building-line"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-semibold">{{ $organizacion->nombre }}</h6>
                                            <small class="text-muted">{{ $organizacion->codigo }}</small>
                                            @if($organizacion->direccion)
                                                <br><small class="text-muted d-block mt-1">
                                                    <i class="ri-map-pin-line me-1"></i>{{ Str::limit($organizacion->direccion, 40) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $tipoText = '';
                                        switch(strtolower($organizacion->tipo)) {
                                            case 'nacional':
                                                $tipoText = 'Nacional';
                                                break;
                                            case 'seccional':
                                                $tipoText = 'Seccional';
                                                break;
                                            case 'seccional_internacional':
                                                $tipoText = 'Seccional Internacional';
                                                break;
                                            case 'diaspora':
                                                $tipoText = 'Diaspora';
                                                break;
                                            default:
                                                $tipoText = ucfirst($organizacion->tipo);
                                        }
                                    @endphp
                                    <span style="color: #000000; font-weight: 600;">{{ $tipoText }}</span>
                                </td>
                                <td>
                                    @php
                                        $estadoText = '';
                                        $estadoColor = '';
                                        switch(strtolower($organizacion->estado)) {
                                            case 'suspendida':
                                                $estadoText = 'Suspendida';
                                                $estadoColor = 'rgb(108, 117, 125)';
                                                break;
                                            case 'inactiva':
                                                $estadoText = 'Inactiva';
                                                $estadoColor = 'rgb(255, 99, 132)';
                                                break;
                                            case 'activa':
                                                $estadoText = 'Activa';
                                                $estadoColor = 'rgb(40, 167, 69)';
                                                break;
                                            default:
                                                $estadoText = ucfirst($organizacion->estado);
                                                $estadoColor = '#6c757d';
                                        }
                                    @endphp
                                    <span style="color: {{ $estadoColor }}; font-weight: 600;">{{ $estadoText }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $organizacion->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organizaciones.profile', $organizacion->id) }}" 
                                           class="btn btn-soft-primary btn-sm" 
                                           title="Ver Perfil">
                                            <i class="ri-building-line"></i>
                                        </a>
                                        <a href="{{ route('organizaciones.edit', $organizacion->id) }}" 
                                           class="btn btn-soft-warning btn-sm" 
                                           title="Editar">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-soft-danger btn-sm" 
                                                title="Eliminar"
                                                onclick="deleteOrganizacion({{ $organizacion->id }}, '{{ addslashes($organizacion->nombre) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ri-building-line fs-1 mb-3 d-block"></i>
                                            <h5>No hay organizaciones registradas</h5>
                                            <p>Comience agregando su primera organización.</p>
                                            <a href="{{ route('organizaciones.create') }}" class="btn btn-primary">
                                                <i class="ri-building-add-line me-1"></i> Agregar Primera Organización
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación Mejorada CLDCI -->
                @if($organizaciones->hasPages() || $organizaciones->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $organizaciones->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $organizaciones->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $organizaciones->total() }}</strong> 
                                    organizaciones
                                </span>
                            </div>
                        </div>
                        
                        <!-- Controles de paginación -->
                        <div class="col-md-6">
                            <div class="cldci-pagination">
                                <!-- Botón Primera Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaOrganizaciones(1)" 
                                        title="Primera página" 
                                        {{ $organizaciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaOrganizaciones({{ $organizaciones->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $organizaciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $organizaciones->currentPage();
                                        $lastPage = $organizaciones->lastPage();
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
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPaginaOrganizaciones(1)">1</button>
                                        @if($startPage > 2)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                    @endif

                                    @for($i = $startPage; $i <= $endPage; $i++)
                                        <button type="button" 
                                                class="pagination-btn pagination-number {{ $i == $currentPage ? 'active' : '' }}" 
                                                onclick="cambiarPaginaOrganizaciones({{ $i }})">
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPaginaOrganizaciones({{ $lastPage }})">{{ $lastPage }}</button>
                                    @endif
                                </div>
                                
                                <!-- Botón Página Siguiente -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaOrganizaciones({{ $organizaciones->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $organizaciones->currentPage() >= $organizaciones->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaOrganizaciones({{ $organizaciones->lastPage() }})" 
                                        title="Última página" 
                                        {{ $organizaciones->currentPage() >= $organizaciones->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ $organizaciones->lastPage() > 0 ? ($organizaciones->currentPage() / $organizaciones->lastPage()) * 100 : 0 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $organizaciones->currentPage() }} de {{ $organizaciones->lastPage() }}
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
    // Verificar si jQuery está disponible antes de usar
    if (typeof $ !== 'undefined') {
        // Los filtros funcionan sin Select2
        // Auto-submit form on filter change
        $('#tipo, #estado').on('change', function() {
            $('#organizaciones-filters-form').submit();
        });

        // Efectos de carga para botón buscar
        $('#organizaciones-filters-form').on('submit', function() {
            const btnBuscar = $('.btn-buscar');
            btnBuscar.addClass('loading');
            btnBuscar.html('<i class="ri-loader-4-line me-1"></i> Buscando...');
        });

        // Efectos hover mejorados
        $('.btn-buscar').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );

        $('.btn-limpiar').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );
    } else {
        console.log('jQuery o Select2 no están disponibles, usando funcionalidad básica');
    }
});

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

function exportarOrganizaciones() {
    const form = document.getElementById('organizaciones-filters-form');
    const action = form.action;
    form.action = '{{ route("organizaciones.exportar") }}';
    form.submit();
    form.action = action;
}

function imprimirLista() {
    window.print();
}

function cambiarPaginaOrganizaciones(pagina) {
    if (pagina < 1 || pagina > {{ $organizaciones->lastPage() }}) return;
    
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

// Funciones para manejo de selección de organizaciones
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const organizacionCheckboxes = document.querySelectorAll('.organizacion-checkbox');
    
    organizacionCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const organizacionCheckboxes = document.querySelectorAll('.organizacion-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectedCount = document.getElementById('selectedCount');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    
    const checkedBoxes = document.querySelectorAll('.organizacion-checkbox:checked');
    const totalBoxes = organizacionCheckboxes.length;
    
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

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.organizacion-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos una organización');
        return;
    }
    
    switch(action) {
        case 'export':
            exportSelectedOrganizaciones(selectedIds);
            break;
        case 'print':
            printSelectedOrganizaciones(selectedIds);
            break;
        case 'email':
            sendEmailToSelected(selectedIds);
            break;
        case 'status':
            changeStatusOfSelected(selectedIds);
            break;
        case 'delete':
            deleteSelectedOrganizaciones(selectedIds);
            break;
        default:
            console.log('Acción no reconocida:', action);
    }
}

function exportSelectedOrganizaciones(selectedIds) {
    // Crear formulario temporal para exportar solo las seleccionadas
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("organizaciones.exportar") }}';
    
    // Agregar CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Agregar IDs seleccionados
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function printSelectedOrganizaciones(selectedIds) {
    console.log('Imprimiendo organizaciones:', selectedIds);
    // Implementar lógica de impresión
    alert('Funcionalidad de impresión en desarrollo');
}

function sendEmailToSelected(selectedIds) {
    console.log('Enviando email a organizaciones:', selectedIds);
    // Implementar lógica de envío de email
    alert('Funcionalidad de email en desarrollo');
}

function changeStatusOfSelected(selectedIds) {
    console.log('Cambiando estado de organizaciones:', selectedIds);
    // Implementar lógica de cambio de estado
    alert('Funcionalidad de cambio de estado en desarrollo');
}

function deleteSelectedOrganizaciones(selectedIds) {
    if (!confirm(`¿Está seguro de eliminar ${selectedIds.length} organización(es) seleccionada(s)? Esta acción no se puede deshacer.`)) {
        return;
    }
    
    // Realizar eliminación por AJAX
    fetch('{{ route("organizaciones.bulk-delete") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            selected_ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof showSuccessToast === 'function') {
                showSuccessToast(data.message || 'Organizaciones eliminadas exitosamente');
            }
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Error al eliminar las organizaciones');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showErrorToast === 'function') {
            showErrorToast('Error al eliminar las organizaciones');
        } else {
            alert('Error al eliminar las organizaciones');
        }
    });
}

// Función para mostrar modal de confirmación de eliminación
function showDeleteConfirmation(organizacionId, organizacionName) {
    // Actualizar el texto del modal
    document.getElementById('deleteConfirmationText').textContent = 
        `¿Está seguro de eliminar la organización "${organizacionName}"? Esta acción no se puede deshacer.`;
    
    // Mostrar el modal usando Bootstrap
    const modalElement = document.getElementById('deleteConfirmationModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Configurar el botón de confirmación
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        performDelete(organizacionId);
        modal.hide();
    };
}

// Función global para eliminar organización
function deleteOrganizacion(organizacionId, organizacionName) {
    // Mostrar modal de confirmación
    showDeleteConfirmation(organizacionId, organizacionName);
}

// Función para realizar la eliminación real
function performDelete(organizacionId) {
    // Mostrar toast de carga
    if (typeof showInfoToast === 'function') {
        showInfoToast('Eliminando organización...', 'Procesando');
    }
    
    // Realizar eliminación por AJAX
    fetch(`{{ url('organizaciones') }}/${organizacionId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => {
                if (response.ok && data.success) {
                    // Mostrar toast de éxito
                    if (typeof showSuccessToast === 'function') {
                        showSuccessToast(data.message || 'Organización eliminada exitosamente');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Organización eliminada exitosamente',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                    
                    // Recargar la página después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error al eliminar la organización');
                }
            });
        } else {
            // Si no es JSON, verificar el status
            if (response.ok) {
                // Mostrar toast de éxito
                if (typeof showSuccessToast === 'function') {
                    showSuccessToast('Organización eliminada exitosamente');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Organización eliminada exitosamente',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
                
                // Recargar la página después de un breve delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error('Error al eliminar la organización');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showErrorToast === 'function') {
            showErrorToast(error.message || 'Error al eliminar la organización');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: error.message || 'Error al eliminar la organización',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(error.message || 'Error al eliminar la organización');
        }
    });
}

// Función para abrir modal de configuración
function openConfigModal(organizacionId, organizacionName) {
    // Implementar modal de configuración
    console.log('Abriendo configuración para:', organizacionId, organizacionName);
    alert(`Configuración de ${organizacionName} (ID: ${organizacionId})`);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay un mensaje de éxito en la sesión (solo una vez)
    @if(session('success'))
        // Mostrar toast de éxito inmediatamente
        if (typeof window.showSuccessToast === 'function') {
            window.showSuccessToast('{{ session('success') }}');
        } else {
            console.log('Toast de éxito:', '{{ session('success') }}');
        }
    @endif

    // Inicializar tooltips para los iconos de acción
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
});
</script>

{{-- Modal de Confirmación de Eliminación --}}
@include('components.modals.delete-confirmation')

@endsection