@extends('partials.layouts.master')

@section('title', 'Cuotas | CLDCI')
@section('title-sub', 'Gestión de Cuotas')
@section('pagetitle', 'Cuotas')

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
    
    /* ==========================================
       ICONOS DE ACCIONES - DISEÑO COHERENTE MEJORADO
       ========================================== */
    
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
        min-width: 1200px !important;
        width: max-content !important;
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
    
    /* Animaciones para dropdowns */
    @keyframes dropdownSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes dropdownSlideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Asegurar que el desplegable esté por encima de otros elementos */
    .table-responsive {
        position: relative;
        z-index: 1;
    }
    
    .cuotas-table tbody tr {
        position: relative;
        z-index: 1;
    }
    
    .cuotas-table tbody tr:hover {
        z-index: 2;
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
                <x-miembros.stat-card
                    title="Total Cuotas"
                    :value="number_format($estadisticas['total_cuotas'])"
                    icon="ri-file-list-3-line"
                    background="bg-primary-subtle"
                    icon-background="bg-primary"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Pendientes"
                    :value="number_format($estadisticas['pendientes'])"
                    icon="ri-time-line"
                    background="bg-warning-subtle"
                    icon-background="bg-warning"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Pagadas"
                    :value="number_format($estadisticas['pagadas'])"
                    icon="ri-check-line"
                    background="bg-success-subtle"
                    icon-background="bg-success"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Vencidas"
                    :value="number_format($estadisticas['vencidas'])"
                    icon="ri-close-line"
                    background="bg-danger-subtle"
                    icon-background="bg-danger"
                />
            </div>
        </div>
    </div>

    <!-- Filtros Globales -->
    <x-global-filter
        title="Filtros de Cuotas"
        description="Refine los resultados de cuotas utilizando los filtros disponibles"
        icon="ri-money-dollar-circle-line"
        form-id="cuotas-filters-form"
        form-action="{{ route('cuotas.index') }}"
        clear-url="{{ route('cuotas.index') }}"
        submit-label="Buscar"
        clear-label="Limpiar"
        variant="compact"
        :filters="[
            [
                'name' => 'estado',
                'label' => 'Estado de Cuota',
                'type' => 'select',
                'placeholder' => 'Todos los estados',
                'col' => 'col-md-3',
                'options' => [
                    'pendiente' => 'Pendiente',
                    'pagada' => 'Pagada',
                    'vencida' => 'Vencida'
                ]
            ],
            [
                'name' => 'tipo_cuota',
                'label' => 'Tipo de Cuota',
                'type' => 'select',
                'placeholder' => 'Todos los tipos',
                'col' => 'col-md-3',
                'options' => [
                    'mensual' => 'Mensual',
                    'trimestral' => 'Trimestral',
                    'anual' => 'Anual'
                ]
            ],
            [
                'name' => 'miembro_id',
                'label' => 'Miembro',
                'type' => 'select',
                'placeholder' => 'Todos los miembros',
                'col' => 'col-md-4',
                'options' => collect($miembros)->mapWithKeys(function($miembro) {
                    return [$miembro->id => $miembro->nombre_completo . ' (' . $miembro->numero_carnet . ')'];
                })->toArray()
            ],
            [
                'name' => 'fecha_vencimiento',
                'label' => 'Vence el Día',
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

    <!-- Tabla de Cuotas -->
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('cuotas.create') }}" class="btn btn-agregar">
                            <i class="ri-add-line"></i>
                            <span>Nueva Cuota</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-money-dollar-circle-line"></i>
                            Lista de Cuotas
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Gestione y administre todas las cuotas de membresía del sistema
                        </p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#generarCuotasModal">
                            <i class="ri-add-circle-line me-1"></i> Generar Cuotas
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarCuotas()">
                            <i class="ri-download-line me-1"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="imprimirCuotas()">
                            <i class="ri-printer-line me-1"></i> Imprimir
                        </button>
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
                                <li><a class="dropdown-item" href="#" onclick="bulkAction('mark_paid')">
                                    <i class="ri-check-line me-2"></i> Marcar como Pagadas
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                                    <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionadas
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap cuotas-table">
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
                                    Miembro <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="monto">
                                    Monto <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_vencimiento">
                                    Vence el Día <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="estado">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="recurrente">
                                    Recurrente <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuotas as $cuota)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input cuota-checkbox" type="checkbox" 
                                               value="{{ $cuota->id }}" 
                                               id="cuota_{{ $cuota->id }}"
                                               onchange="updateSelectAllState()">
                                        <label class="form-check-label" for="cuota_{{ $cuota->id }}">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="ri-user-line text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $cuota->miembro->nombre_completo }}</h6>
                                            <small class="text-muted">
                                                <i class="ri-id-card-line me-1"></i>{{ $cuota->miembro->numero_carnet }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>RD$ {{ number_format($cuota->monto, 2) }}</strong>
                                </td>
                                <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-{{ $cuota->estado_color }}-subtle rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="ri-{{ $cuota->estado === 'pagada' ? 'check' : ($cuota->estado === 'vencida' ? 'close' : 'time') }}-line text-{{ $cuota->estado_color }}"></i>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $cuota->estado_color }}-subtle text-{{ $cuota->estado_color }}">
                                                {{ ucfirst($cuota->estado) }}
                                            </span>
                                            <div class="text-muted small">
                                                @if($cuota->estado === 'pendiente')
                                                    Pendiente de pago
                                                @elseif($cuota->estado === 'pagada')
                                                    Pagada el {{ $cuota->fecha_pago->format('d/m/Y') }}
                                                @elseif($cuota->estado === 'vencida')
                                                    Vencida el {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                                                @else
                                                    {{ ucfirst($cuota->estado) }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($cuota->recurrente)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="ri-refresh-line me-1"></i>Sí
                                            </span>
                                            @if($cuota->frecuencia_recurrencia)
                                                <div class="text-muted small ms-2">
                                                    {{ ucfirst($cuota->frecuencia_recurrencia) }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="ri-close-line me-1"></i>No
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <!-- Primera fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Ver Detalles -->
                                            <a href="{{ route('cuotas.show', $cuota) }}" 
                                               class="btn btn-soft-primary btn-sm" 
                                               title="Ver Detalles"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-eye-line fs-4"></i>
                                            </a>
                                            
                                            <!-- Editar -->
                                            <a href="{{ route('cuotas.edit', $cuota) }}" 
                                               class="btn btn-soft-warning btn-sm" 
                                               title="Editar"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-edit-line fs-4"></i>
                                            </a>
                                            
                                            <!-- Marcar como Pagada (solo si está pendiente) -->
                                            @if($cuota->estado === 'pendiente')
                                            <button type="button" 
                                                    class="btn btn-soft-success btn-sm" 
                                                    title="Marcar como Pagada"
                                                    data-bs-toggle="tooltip"
                                                    onclick="marcarComoPagada({{ $cuota->id }})">
                                                <i class="ri-check-line fs-4"></i>
                                            </button>
                                            @endif
                                        </div>
                                        
                                        <!-- Segunda fila de iconos -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Eliminar -->
                                            <button type="button" 
                                                    class="btn btn-soft-danger btn-sm" 
                                                    title="Eliminar"
                                                    data-bs-toggle="tooltip"
                                                    onclick="deleteCuota({{ $cuota->id }}, '{{ $cuota->miembro->nombre ?? 'Cuota' }}')">
                                                <i class="ri-delete-bin-line fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-file-list-3-line font-size-48 mb-3 d-block"></i>
                                        <h5>No hay cuotas registradas</h5>
                                        <p>Comienza creando una nueva cuota o generando cuotas automáticamente.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Paginación Mejorada CLDCI -->
                @if($cuotas->hasPages() || $cuotas->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $cuotas->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $cuotas->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $cuotas->total() }}</strong> 
                                    cuotas
                                </span>
                            </div>
                        </div>
                        
                        <!-- Controles de paginación -->
                        <div class="col-md-6">
                            <div class="cldci-pagination">
                                <!-- Botón Primera Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaCuotas(1)" 
                                        title="Primera página" 
                                        {{ $cuotas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaCuotas({{ $cuotas->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $cuotas->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $cuotas->currentPage();
                                        $lastPage = $cuotas->lastPage();
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
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPaginaCuotas(1)">1</button>
                                        @if($startPage > 2)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                    @endif

                                    @for($i = $startPage; $i <= $endPage; $i++)
                                        <button type="button" 
                                                class="pagination-btn pagination-number {{ $i == $currentPage ? 'active' : '' }}" 
                                                onclick="cambiarPaginaCuotas({{ $i }})">
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <span class="pagination-ellipsis">...</span>
                                        @endif
                                        <button type="button" class="pagination-btn pagination-number" onclick="cambiarPaginaCuotas({{ $lastPage }})">{{ $lastPage }}</button>
                                    @endif
                                </div>
                                
                                <!-- Botón Página Siguiente -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaCuotas({{ $cuotas->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $cuotas->currentPage() >= $cuotas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPaginaCuotas({{ $cuotas->lastPage() }})" 
                                        title="Última página" 
                                        {{ $cuotas->currentPage() >= $cuotas->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($cuotas->currentPage() / $cuotas->lastPage()) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $cuotas->currentPage() }} de {{ $cuotas->lastPage() }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para marcar como pagada -->
<div class="modal fade" id="marcarPagadaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marcar Cuota como Pagada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="marcarPagadaForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas sobre el pago..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Pagada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para generar cuotas -->
<div class="modal fade" id="generarCuotasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Cuotas Automáticamente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cuotas.generar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo_cuota_generar" class="form-label">Tipo de Cuota</label>
                        <select class="form-select" id="tipo_cuota_generar" name="tipo_cuota" required>
                            <option value="mensual">Mensual</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monto_generar" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto_generar" name="monto" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_periodos" class="form-label">Cantidad de Períodos</label>
                        <input type="number" class="form-control" id="cantidad_periodos" name="cantidad_periodos" min="1" max="12" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Cuotas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Mostrar toast de éxito desde sesión (por si la eliminación no fue AJAX)
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        if (typeof window.showSuccessToast === 'function') {
            window.showSuccessToast('{{ session('success') }}');
        }
    @endif
    // Toast persistente tras reload (vía sessionStorage)
    try {
        const t = sessionStorage.getItem('cuotas_toast_success');
        if (t) {
            const show = () => {
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast(t);
                    sessionStorage.removeItem('cuotas_toast_success');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title:t, showConfirmButton:false, timer:3000, timerProgressBar:true });
                    sessionStorage.removeItem('cuotas_toast_success');
                } else {
                    setTimeout(show, 150);
                }
            };
            setTimeout(show, 200);
        }
    } catch(_) {}
});
// Fallback: definir showDeleteConfirmation con el modal global si no existe
if (typeof window.showDeleteConfirmation === 'undefined') {
    window.showDeleteConfirmation = function(options) {
        const modalEl = document.getElementById('deleteConfirmationModal');
        const textEl = document.getElementById('deleteConfirmationText');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (!modalEl || !textEl || !confirmBtn) {
            // Si por alguna razón no existe el modal, usamos confirm nativo
            if (confirm(options?.title ? `¿Eliminar ${options.title}?` : '¿Eliminar?')) {
                if (typeof options.onConfirm === 'function') options.onConfirm();
            }
            return;
        }
        // Mensaje
        textEl.textContent = options?.title ? `¿Está seguro de eliminar "${options.title}"?` : '¿Está seguro de eliminar?';
        // Click de confirmación (limpiar handlers previos)
        const newHandler = function() {
            const bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) bsModal.hide();
            if (typeof options.onConfirm === 'function') options.onConfirm();
            confirmBtn.removeEventListener('click', newHandler);
        };
        confirmBtn.addEventListener('click', newHandler);
        // Mostrar modal
        const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
        bsModal.show();
    };
}
function marcarComoPagada(cuotaId) {
    const form = document.getElementById('marcarPagadaForm');
    form.action = `/cuotas/${cuotaId}/marcar-pagada`;
    
    const modal = new bootstrap.Modal(document.getElementById('marcarPagadaModal'));
    modal.show();
}

function exportarCuotas() {
    const form = document.getElementById('filtros-form');
    const action = form.action;
    form.action = '{{ route("cuotas.exportar") }}';
    form.submit();
    form.action = action;
}

function imprimirCuotas() {
    window.print();
}

function deleteCuota(cuotaId, cuotaName) {
    if (typeof showDeleteConfirmation === 'function') {
        showDeleteConfirmation({
            title: cuotaName,
            type: 'cuota',
            onConfirm: () => executeCuotaDelete(cuotaId, cuotaName)
        });
    } else {
        if (confirm(`¿Eliminar la cuota de "${cuotaName}"?`)) {
            executeCuotaDelete(cuotaId, cuotaName);
        }
    }
}

function executeCuotaDelete(cuotaId, cuotaName) {
    // Estrategia: persistir mensaje y recargar rápido para asegurar toast post-reload
    fetch(`{{ url('cuotas') }}/${cuotaId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            try { sessionStorage.setItem('cuotas_toast_success', `Cuota \"${cuotaName}\" eliminada exitosamente`); } catch(_) {}
            setTimeout(() => { window.location.reload(); }, 100);
        } else {
            return response.text().then(t => { throw new Error(t || 'Error al eliminar la cuota'); });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showErrorToast === 'function') showErrorToast('Error al eliminar la cuota');
    });
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

function cambiarPaginaCuotas(pagina) {
    if (pagina < 1 || pagina > {{ $cuotas->lastPage() }}) return;
    
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

// Funciones para selección múltiple
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const cuotaCheckboxes = document.querySelectorAll('.cuota-checkbox');
    
    cuotaCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const cuotaCheckboxes = document.querySelectorAll('.cuota-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectedCount = document.getElementById('selectedCount');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    
    const checkedBoxes = document.querySelectorAll('.cuota-checkbox:checked');
    const totalBoxes = cuotaCheckboxes.length;
    
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
    const selectedIds = Array.from(document.querySelectorAll('.cuota-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos una cuota.');
        return;
    }
    
    switch(action) {
        case 'export':
            exportSelectedCuotas(selectedIds);
            break;
        case 'print':
            printSelectedCuotas(selectedIds);
            break;
        case 'mark_paid':
            markSelectedAsPaid(selectedIds);
            break;
        case 'delete':
            deleteSelectedCuotas(selectedIds);
            break;
    }
}

function exportSelectedCuotas(selectedIds) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("cuotas.exportar") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
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

function printSelectedCuotas(selectedIds) {
    // Implementar lógica de impresión para cuotas seleccionadas
    alert(`Imprimiendo ${selectedIds.length} cuotas seleccionadas...`);
}

function markSelectedAsPaid(selectedIds) {
    if (confirm(`¿Está seguro de marcar ${selectedIds.length} cuotas como pagadas?`)) {
        // Implementar lógica para marcar como pagadas
        alert(`Marcando ${selectedIds.length} cuotas como pagadas...`);
    }
}

function deleteSelectedCuotas(selectedIds) {
    showDeleteConfirmation({
        title: `${selectedIds.length} cuotas seleccionadas`,
        message: `Se eliminarán ${selectedIds.length} cuotas seleccionadas permanentemente.`,
        type: 'múltiples cuotas',
        onConfirm: () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("cuotas.bulk-delete") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
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
            
            // Mostrar toast de éxito
            showSuccessToast(`${selectedIds.length} cuotas eliminadas exitosamente`);
        }
    });
}

// Sorting de columnas
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay un mensaje de éxito en la sesión
    @if(session('success'))
        // Mostrar toast de éxito inmediatamente
        if (typeof window.showSuccessToast === 'function') {
            window.showSuccessToast('{{ session('success') }}');
        } else {
            console.log('Toast de éxito:', '{{ session('success') }}');
        }
    @endif

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
