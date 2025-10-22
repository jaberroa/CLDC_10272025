@extends('partials.layouts.master')

@section('title', 'Miembros | CLDCI')
@section('title-sub', 'Gestión de Miembros')
@section('pagetitle', 'Miembros')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/qrcode/qrcode.min.css') }}">
<style>
    .carnet-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
        overflow: hidden;
    }

    .carnet-modal-content {
        background: white;
        border-radius: 15px;
        width: 95vw;
        max-width: 350px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: modalSlideIn 0.4s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .carnet-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .carnet-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .carnet-modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .carnet-modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .carnet-modal-body {
        padding: 1.5rem;
        background: #f8f9fa;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carnet-digital-container {
        max-width: 350px;
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.25rem;
        color: white;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
        margin: 0 auto;
    }

    .carnet-digital-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    .carnet-header {
        text-align: center;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .carnet-logo {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
    }

    .carnet-photo {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.4);
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .carnet-info {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        padding: 0.75rem;
        margin: 0.5rem 0;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .carnet-qr {
        text-align: center;
        margin: 1rem 0;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 0.75rem;
        backdrop-filter: blur(10px);
    }

    .carnet-footer {
        text-align: center;
        font-size: 0.75rem;
        opacity: 0.9;
        margin-top: 1rem;
        position: relative;
        z-index: 2;
    }

    .carnet-number {
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        letter-spacing: 1px;
    }

    .carnet-name {
        font-size: 1rem;
        font-weight: 700;
        margin: 0.5rem 0 0.25rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    .carnet-profession {
        font-size: 0.85rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .carnet-org {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 0.25rem;
    }

    .carnet-status {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .carnet-status.activa {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .carnet-modal-actions {
        padding: 1rem 1.5rem;
        background: white;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .carnet-modal-actions .btn {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    .carnet-modal-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    /* ==========================================
       DROPDOWN DE ACCIONES - MEJORAS UX
       ========================================== */
    
    /* Mejorar z-index del dropdown de acciones */
    .table-responsive .dropdown {
        position: relative;
        z-index: 1050;
    }
    
    .table-responsive .dropdown-menu {
        z-index: 1055 !important;
        position: absolute !important;
        top: 100% !important;
        left: auto !important;
        right: 0 !important;
        transform: none !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        border-radius: 0.5rem !important;
        min-width: 200px !important;
        padding: 0.5rem 0 !important;
        background: white !important;
        animation: dropdownSlideIn 0.2s ease-out !important;
    }
    
    @keyframes dropdownSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* Mejorar botón del dropdown */
    .table-responsive .dropdown-toggle {
        border: none !important;
        background: transparent !important;
        color: #6c757d !important;
        padding: 0.5rem !important;
        border-radius: 0.375rem !important;
        transition: all 0.2s ease !important;
        position: relative !important;
        z-index: 1051 !important;
    }
    
    .table-responsive .dropdown-toggle:hover {
        background: rgba(108, 117, 125, 0.1) !important;
        color: #495057 !important;
        transform: scale(1.05) !important;
    }
    
    .table-responsive .dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25) !important;
        outline: none !important;
    }
    
    /* Mejorar items del dropdown */
    .table-responsive .dropdown-item {
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        color: #495057 !important;
        transition: all 0.2s ease !important;
        border-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }
    
    .table-responsive .dropdown-item:hover {
        background: rgba(108, 117, 125, 0.1) !important;
        color: #212529 !important;
        transform: translateX(4px) !important;
    }
    
    .table-responsive .dropdown-item i {
        width: 16px !important;
        height: 16px !important;
        font-size: 16px !important;
        color: #6c757d !important;
    }
    
    .table-responsive .dropdown-item:hover i {
        color: #495057 !important;
    }
    
    /* Separadores del dropdown */
    .table-responsive .dropdown-divider {
        margin: 0.5rem 0 !important;
        border-color: rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Item de eliminar con estilo especial */
    .table-responsive .dropdown-item.text-danger {
        color: #dc3545 !important;
    }
    
    .table-responsive .dropdown-item.text-danger:hover {
        background: rgba(220, 53, 69, 0.1) !important;
        color: #b02a37 !important;
    }
    
    .table-responsive .dropdown-item.text-danger i {
        color: #dc3545 !important;
    }
    
    .table-responsive .dropdown-item.text-danger:hover i {
        color: #b02a37 !important;
    }
    
    /* Asegurar que el dropdown se muestre por encima de otros elementos */
    .table-responsive {
        overflow: visible !important;
    }
    
    .table-responsive .table {
        margin-bottom: 0 !important;
    }
    
    /* Mejoras para dispositivos móviles */
    @media (max-width: 768px) {
        .table-responsive .dropdown-menu {
            min-width: 180px !important;
            right: -10px !important;
        }
        
        .table-responsive .dropdown-item {
            padding: 0.625rem 0.875rem !important;
            font-size: 0.8rem !important;
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

    @media (max-width: 768px) {
        .carnet-modal-content {
            width: 98vw;
            max-width: 98vw;
            margin: 0.5rem;
        }
        
        .carnet-modal-body {
            padding: 1rem;
            min-height: 250px;
        }
        
        .carnet-digital-container {
            padding: 1rem;
            max-width: 100%;
        }
        
        .carnet-modal-actions {
            padding: 0.75rem;
            flex-direction: column;
        }
        
        .carnet-modal-actions .btn {
            width: 100%;
            margin: 0.25rem 0;
        }
        
        .carnet-photo {
            width: 60px;
            height: 60px;
        }
        
        .carnet-logo {
            width: 40px;
            height: 40px;
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
                    title="Total Miembros"
                    :value="number_format($estadisticas['total_miembros'])"
                    icon="ri-user-line"
                    background="bg-primary-subtle"
                    icon-background="bg-primary"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Miembros Activos"
                    :value="number_format($estadisticas['miembros_activos'])"
                    icon="ri-user-star-line"
                    background="bg-success-subtle"
                    icon-background="bg-success"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Organizaciones"
                    :value="number_format($organizaciones->count())"
                    icon="ri-building-line"
                    background="bg-info-subtle"
                    icon-background="bg-info"
                />
            </div>
            <div class="col-xxl col-sm-6">
                <x-miembros.stat-card
                    title="Nuevos Este Mes"
                    :value="number_format($estadisticas['nuevos_este_mes'])"
                    icon="ri-calendar-line"
                    background="bg-warning-subtle"
                    icon-background="bg-warning"
                />
            </div>
        </div>
    </div>

    <!-- Filtros Globales -->
    <x-global-filter
        title="Filtros de Búsqueda"
        description="Refine los resultados utilizando los filtros disponibles"
        icon="ri-search-line"
        form-id="miembros-filters-form"
        form-action="{{ route('miembros.index') }}"
        clear-url="{{ route('miembros.index') }}"
        submit-label="Buscar"
        clear-label="Limpiar"
        variant="default"
        :filters="[
            [
                'name' => 'buscar',
                'label' => 'Buscar',
                'type' => 'text',
                'placeholder' => 'Nombre, cédula o carnet',
                'col' => 'col-md-4'
            ],
            [
                'name' => 'tipo_membresia',
                'label' => 'Tipo de Membresía',
                'type' => 'select',
                'placeholder' => 'Todos los tipos',
                'col' => 'col-md-3',
                'options' => [
                    'fundador' => 'Fundador',
                    'activo' => 'Activo',
                    'pasivo' => 'Pasivo',
                    'honorifico' => 'Honorífico',
                    'estudiante' => 'Estudiante',
                    'diaspora' => 'Diáspora'
                ]
            ],
            [
                'name' => 'estado_membresia',
                'label' => 'Estado',
                'type' => 'select',
                'placeholder' => 'Todos los estados',
                'col' => 'col-md-3',
                'options' => [
                    'activa' => 'Activa',
                    'suspendida' => 'Suspendida',
                    'inactiva' => 'Inactiva',
                    'honoraria' => 'Honoraria'
                ]
            ],
            [
                'name' => 'organizacion_id',
                'label' => 'Organización',
                'type' => 'select',
                'placeholder' => 'Todas las organizaciones',
                'col' => 'col-md-2',
                'options' => $organizaciones->pluck('nombre', 'id')->toArray()
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

    <!-- Tabla de Miembros -->
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('miembros.create') }}" class="btn btn-agregar">
                            <i class="ri-user-add-line"></i>
                            <span>Agregar Miembro</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-group-line"></i>
                            Lista de Miembros
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Gestione y administre todos los miembros registrados en el sistema
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
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarMiembros()">
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
                                <th class="sortable" data-sort="nombre_completo">
                                    Miembro <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="numero_carnet">
                                    Número Carnet <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="tipo_membresia">
                                    Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="estado_membresia">
                                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="organizacion_id">
                                    Organización <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th class="sortable" data-sort="fecha_ingreso">
                                    Fecha Ingreso <i class="ri-arrow-up-down-line ms-1"></i>
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($miembros as $miembro)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input member-checkbox" type="checkbox" 
                                               value="{{ $miembro->id }}" 
                                               id="member_{{ $miembro->id }}"
                                               onchange="updateSelectAllState()">
                                        <label class="form-check-label" for="member_{{ $miembro->id }}">
                                            <i class="ri-checkbox-line"></i>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($miembro->foto_url)
                                            <img src="{{ $miembro->foto_url }}" alt="" class="avatar-xs rounded-circle">
                                            @else
                                            <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                <i class="ri-user-line fs-12"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0">{{ $miembro->nombre_completo }}</h6>
                                            <p class="text-muted mb-0 fs-12">{{ $miembro->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $miembro->numero_carnet }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center tipo-membresia-container">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="ri-user-star-line text-primary fs-10"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-semibold text-dark">Miembro</span>
                                            <br>
                                            <small class="text-muted">Tipo estándar</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center estado-membresia-container">
                                        <div class="flex-shrink-0 me-2">
                                            @if($miembro->estadoMembresia)
                                                @if($miembro->estadoMembresia->nombre === 'activa')
                                                    <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-check-line text-success fs-10"></i>
                                                    </div>
                                                @elseif($miembro->estadoMembresia->nombre === 'suspendida')
                                                    <div class="avatar-xs bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-pause-line text-danger fs-10"></i>
                                                    </div>
                                                @else
                                                    <div class="avatar-xs bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-time-line text-warning fs-10"></i>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="avatar-xs bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-question-line text-secondary fs-10"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            @if($miembro->estadoMembresia)
                                                <span class="badge bg-{{ $miembro->estadoMembresia->nombre === 'activa' ? 'success' : ($miembro->estadoMembresia->nombre === 'suspendida' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $miembro->estadoMembresia->nombre === 'activa' ? 'success' : ($miembro->estadoMembresia->nombre === 'suspendida' ? 'danger' : 'warning') }} border border-{{ $miembro->estadoMembresia->nombre === 'activa' ? 'success' : ($miembro->estadoMembresia->nombre === 'suspendida' ? 'danger' : 'warning') }} border-opacity-25 fw-semibold">
                                                    {{ ucfirst($miembro->estadoMembresia->nombre) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 fw-semibold">
                                                    Sin Estado
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                @if($miembro->estadoMembresia)
                                                    {{ $miembro->estadoMembresia->descripcion ?? 'Estado de membresía' }}
                                                @else
                                                    Estado no definido
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ Str::limit($miembro->organizacion->nombre, 20) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $miembro->fecha_ingreso->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('miembros.profile', $miembro->id) }}">
                                                <i class="ri-user-line me-2"></i> Ver Perfil
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('carnet.selector', $miembro->id) }}">
                                                <i class="ri-qr-code-line me-2"></i> Carnet Digital
                                            </a></li>
                                            <li><a class="dropdown-item" href="mailto:{{ $miembro->email }}">
                                                <i class="ri-mail-line me-2"></i> Enviar Email
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('miembros.edit', $miembro->id) }}">
                                                <i class="ri-edit-line me-2"></i> Editar
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMember({{ $miembro->id }}, '{{ $miembro->nombre_completo }}')">
                                                <i class="ri-delete-bin-line me-2"></i> Eliminar
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <div class="py-4">
                                        <i class="ri-user-line fs-48 text-muted mb-3 d-block"></i>
                                        <h5>No se encontraron miembros</h5>
                                        <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Paginación Mejorada CLDCI -->
                @if($miembros->hasPages() || $miembros->total() > 0)
                <div class="cldci-pagination-container">
                    <div class="row align-items-center">
                        <!-- Información de resultados -->
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Mostrando 
                                    <strong>{{ $miembros->firstItem() ?? 0 }}</strong> 
                                    a 
                                    <strong>{{ $miembros->lastItem() ?? 0 }}</strong> 
                                    de 
                                    <strong>{{ $miembros->total() }}</strong> 
                                    miembros
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
                                        {{ $miembros->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-skip-back-line"></i>
                                </button>
                                
                                <!-- Botón Página Anterior -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $miembros->currentPage() - 1 }})" 
                                        title="Página anterior" 
                                        {{ $miembros->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line"></i>
                                </button>

                                <!-- Números de página -->
                                <div class="pagination-numbers">
                                    @php
                                        $currentPage = $miembros->currentPage();
                                        $lastPage = $miembros->lastPage();
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
                                        onclick="cambiarPagina({{ $miembros->currentPage() + 1 }})" 
                                        title="Página siguiente" 
                                        {{ $miembros->currentPage() >= $miembros->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-arrow-right-line"></i>
                                </button>
                                
                                <!-- Botón Última Página -->
                                <button type="button" 
                                        class="pagination-btn pagination-btn-nav" 
                                        onclick="cambiarPagina({{ $miembros->lastPage() }})" 
                                        title="Última página" 
                                        {{ $miembros->currentPage() >= $miembros->lastPage() ? 'disabled' : '' }}>
                                    <i class="ri-skip-forward-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicador de progreso -->
                    <div class="pagination-progress mt-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ ($miembros->currentPage() / $miembros->lastPage()) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Página {{ $miembros->currentPage() }} de {{ $miembros->lastPage() }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal del Carnet Digital -->
@include('miembros.partials.carnet-modal')
@endsection

@section('js')
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('#organizacion_id').select2({
        placeholder: 'Seleccionar organización',
        allowClear: true
    });

    // Auto-submit form on filter change
    $('#tipo_membresia, #estado_membresia').on('change', function() {
        $('#filtros-form').submit();
    });

    // Efectos de carga para botón buscar
    $('#filtros-form').on('submit', function() {
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
});

// Funciones para manejo de selección de miembros
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');
    
    memberCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectAllState();
}

function updateSelectAllState() {
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectedCount = document.querySelectorAll('.member-checkbox:checked').length;
    const totalCount = memberCheckboxes.length;
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    const selectedCountBadge = document.getElementById('selectedCount');
    
    // Actualizar estado del checkbox "Seleccionar todo"
    if (selectedCount === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (selectedCount === totalCount) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
    }
    
    // Mostrar/ocultar dropdown de acciones masivas
    if (selectedCount > 0) {
        bulkActionsDropdown.style.display = 'block';
        selectedCountBadge.textContent = selectedCount;
    } else {
        bulkActionsDropdown.style.display = 'none';
    }
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.member-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Por favor seleccione al menos un miembro');
        return;
    }
    
        switch(action) {
            case 'export':
                exportSelectedMembers(selectedIds);
                break;
            case 'print':
                printSelectedMembers(selectedIds);
                break;
            case 'email':
                sendEmailToSelected(selectedIds);
                break;
            case 'status':
                changeStatusOfSelected(selectedIds);
                break;
            case 'delete':
                deleteSelectedMembers(selectedIds);
                break;
            default:
                console.log('Acción no reconocida:', action);
        }
}

function exportSelectedMembers(selectedIds) {
    // Crear formulario temporal para exportar solo los seleccionados
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("miembros.exportar") }}';
    
    // Agregar token CSRF
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
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

function printSelectedMembers(selectedIds) {
    // Implementar impresión de miembros seleccionados
    console.log('Imprimiendo miembros:', selectedIds);
    alert(`Imprimiendo ${selectedIds.length} miembros seleccionados`);
}

function sendEmailToSelected(selectedIds) {
    // Implementar envío de email a miembros seleccionados
    console.log('Enviando email a:', selectedIds);
    alert(`Enviando email a ${selectedIds.length} miembros seleccionados`);
}

function changeStatusOfSelected(selectedIds) {
    // Implementar cambio de estado de miembros seleccionados
    console.log('Cambiando estado de:', selectedIds);
    alert(`Cambiando estado de ${selectedIds.length} miembros seleccionados`);
}

function deleteSelectedMembers(selectedIds) {
    if (confirm(`¿Está seguro de eliminar ${selectedIds.length} miembros seleccionados? Esta acción no se puede deshacer.`)) {
        // Crear formulario para eliminar múltiples miembros
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("miembros.bulk-delete") }}';
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Agregar método DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
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
}

function deleteMember(memberId, memberName) {
    if (confirm(`¿Está seguro de eliminar al miembro "${memberName}"? Esta acción no se puede deshacer.`)) {
        // Crear formulario para eliminar un miembro
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('miembros') }}/${memberId}`;
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Agregar método DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
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

function exportarMiembros() {
    const form = document.getElementById('filtros-form');
    const action = form.action;
    form.action = '{{ route("miembros.exportar") }}';
    form.submit();
    form.action = action;
}

function imprimirLista() {
    window.print();
}

function cambiarPagina(pagina) {
    if (pagina < 1 || pagina > {{ $miembros->lastPage() }}) return;
    
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
    
    // Simular delay para mejor UX (opcional)
    setTimeout(() => {
        const form = document.getElementById('filtros-form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'page';
        input.value = pagina;
        form.appendChild(input);
        form.submit();
    }, 150);
}

// Funciones del Modal del Carnet
function abrirCarnetModal(miembroId) {
    console.log('Abriendo modal para miembro:', miembroId);
    
    // Mostrar loading
    const modal = document.getElementById('carnetModal');
    if (!modal) {
        console.error('Modal no encontrado');
        return;
    }
    modal.style.display = 'flex';
    
    // Cargar datos del miembro via AJAX
    fetch(`/miembros/${miembroId}/carnet-data`)
        .then(response => response.json())
        .then(data => {
            // Llenar datos del modal
            document.getElementById('carnet-miembro-nombre').textContent = data.nombre_completo;
            document.getElementById('carnet-nombre').textContent = data.nombre_completo;
            document.getElementById('carnet-profesion').textContent = data.profesion || 'Locutor';
            document.getElementById('carnet-organizacion').textContent = data.organizacion || 'CLDCI Nacional';
            document.getElementById('carnet-numero').textContent = data.numero_carnet;
            document.getElementById('carnet-tipo-membresia').textContent = data.tipo_membresia || 'Activa';
            document.getElementById('carnet-fecha-ingreso').textContent = data.fecha_ingreso;
            document.getElementById('carnet-valido-hasta').textContent = `Válido hasta: ${data.valido_hasta}`;
            
            // Foto del miembro
            const fotoContainer = document.getElementById('carnet-foto-container');
            if (data.foto_url) {
                fotoContainer.innerHTML = `<img src="${data.foto_url}" alt="Foto" class="carnet-photo">`;
            } else {
                fotoContainer.innerHTML = `<div class="carnet-photo d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);"><i class="ri-user-line" style="font-size: 2rem;"></i></div>`;
            }
            
            // Generar QR Code
            generarQRCode(data);
        })
        .catch(error => {
            console.error('Error cargando datos del carnet:', error);
            alert('Error al cargar los datos del carnet');
            cerrarCarnetModal();
        });
}

function cerrarCarnetModal() {
    const modal = document.getElementById('carnetModal');
    modal.style.display = 'none';
}

function generarQRCode(data) {
    // Limpiar QR anterior
    document.getElementById('qrcode').innerHTML = '';
    
    const qrData = {
        nombre: data.nombre_completo,
        carnet: data.numero_carnet,
        organizacion: data.organizacion || 'CLDCI Nacional',
        tipo: data.tipo_membresia || 'Activa',
        fecha: data.fecha_ingreso,
        url: window.location.origin + `/miembros/${data.id}`,
        timestamp: new Date().toISOString()
    };

    // Cargar librería QR si no está cargada
    if (typeof QRCode === 'undefined') {
        const script = document.createElement('script');
        script.src = '{{ asset("assets/libs/qrcode/qrcode.min.js") }}';
        script.onload = () => generarQR();
        document.head.appendChild(script);
    } else {
        generarQR();
    }
    
    function generarQR() {
        QRCode.toCanvas(document.getElementById('qrcode'), JSON.stringify(qrData), {
            width: 60,
            height: 60,
            margin: 1,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('Error generando QR:', error);
                document.getElementById('qrcode').innerHTML = '<div class="text-center text-muted">QR no disponible</div>';
            }
        });
    }
}

// Funciones de acción del carnet
function imprimirCarnet() {
    const actions = document.querySelector('.carnet-modal-actions');
    const closeBtn = document.querySelector('.carnet-modal-close');
    actions.style.display = 'none';
    closeBtn.style.display = 'none';
    
    window.print();
    
    setTimeout(() => {
        actions.style.display = 'flex';
        closeBtn.style.display = 'flex';
    }, 1000);
}

function descargarCarnet() {
    // Cargar librerías si no están cargadas
    if (typeof html2canvas === 'undefined' || typeof jsPDF === 'undefined') {
        const scripts = [
            'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'
        ];
        
        let loaded = 0;
        scripts.forEach(src => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = () => {
                loaded++;
                if (loaded === scripts.length) {
                    generarPDF();
                }
            };
            document.head.appendChild(script);
        });
    } else {
        generarPDF();
    }
    
    function generarPDF() {
        const carnetContainer = document.querySelector('.carnet-digital-container');
        
        html2canvas(carnetContainer, {
            backgroundColor: null,
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [85, 54]
            });
            
            const imgWidth = 85;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save('carnet-' + document.getElementById('carnet-numero').textContent + '.pdf');
        }).catch(error => {
            console.error('Error generando PDF:', error);
            alert('Error al generar el PDF. Intenta nuevamente.');
        });
    }
}

function compartirCarnet() {
    if (navigator.share) {
        navigator.share({
            title: 'Carnet Digital - ' + document.getElementById('carnet-miembro-nombre').textContent,
            text: 'Mi carnet digital de CLDCI',
            url: window.location.href
        }).catch(error => {
            console.error('Error compartiendo:', error);
            copiarEnlace();
        });
    } else {
        copiarEnlace();
    }
}

function copiarEnlace() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        // Mostrar toast
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="ri-check-line me-2"></i>
                    <strong class="me-auto">Enlace copiado</strong>
                </div>
                <div class="toast-body">
                    El enlace del carnet ha sido copiado al portapapeles.
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(error => {
        console.error('Error copiando enlace:', error);
        alert('No se pudo copiar el enlace. Intenta manualmente.');
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para enlaces del carnet
    document.addEventListener('click', function(e) {
        if (e.target.closest('.carnet-link')) {
            e.preventDefault();
            e.stopPropagation();
            const miembroId = e.target.closest('.carnet-link').getAttribute('data-miembro-id');
            console.log('Clic en carnet para miembro:', miembroId);
            abrirCarnetModal(miembroId);
        }
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarCarnetModal();
        }
    });

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('carnetModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarCarnetModal();
            }
        });
    }
});
</script>
@endsection
