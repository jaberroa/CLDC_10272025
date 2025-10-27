@extends('partials.layouts.master')

@section('title', 'Elecciones | CLDCI')
@section('title-sub', 'Gestión de Elecciones')
@section('pagetitle', 'Elecciones')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/elecciones-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/elecciones-table.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .eleccion-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
        height: 100%;
        background: white;
    }
    
    .eleccion-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .eleccion-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .eleccion-badge.programada {
        background: #e7f3ff;
        color: #0d6efd;
    }
    
    .eleccion-badge.activa {
        background: #d4edda;
        color: #198754;
    }
    
    .eleccion-badge.finalizada {
        background: #f8f9fa;
        color: #6c757d;
    }
    
    .eleccion-badge.cancelada {
        background: #f8d7da;
        color: #dc3545;
    }
    
    .eleccion-badge.cerrada {
        background: #f8f9fa;
        color: #6c757d;
    }
    
    .eleccion-badge.en-curso {
        background: #fff3cd;
        color: #856404;
    }
    
    .countdown-timer {
        font-size: 0.75rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .countdown-timer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #28a745, #20c997);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .countdown-timer:hover::before {
        transform: scaleX(1);
    }
    
    .countdown-timer.urgente {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border-color: #dc3545;
        color: #721c24;
    }
    
    .countdown-timer.urgente::before {
        background: linear-gradient(90deg, #dc3545, #c82333);
    }
    
    .countdown-timer.finalizando {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-color: #ffc107;
        color: #856404;
    }
    
    .countdown-timer.finalizando::before {
        background: linear-gradient(90deg, #ffc107, #e0a800);
    }
    
    .countdown-indicator {
        width: 12px;
        height: 12px;
        position: relative;
    }
    
    .countdown-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #28a745;
        animation: pulse-dot 2s infinite;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .countdown-timer.urgente .countdown-dot {
        background: #dc3545;
        animation: pulse-dot-urgent 1s infinite;
    }
    
    .countdown-timer.finalizando .countdown-dot {
        background: #ffc107;
        animation: pulse-dot-warning 1.5s infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 0.5; transform: translate(-50%, -50%) scale(1.2); }
    }
    
    @keyframes pulse-dot-urgent {
        0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 0.3; transform: translate(-50%, -50%) scale(1.5); }
    }
    
    @keyframes pulse-dot-warning {
        0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.3); }
    }
    
    .progress-custom {
        height: 4px;
        background-color: #e9ecef;
        border-radius: 2px;
    }
    
    .progress-custom .progress-bar {
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 2px;
    }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    .empty-state i {
        opacity: 0.5;
    }
    
    .cldci-pagination-container {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .pagination-info {
        font-size: 0.875rem;
    }
    
    .pagination-info strong {
        color: #495057;
    }
    
    .cldci-pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .cldci-pagination .pagination {
        margin: 0;
    }
    
    .cldci-pagination .page-link {
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        margin: 0 0.125rem;
        transition: all 0.2s ease;
    }
    
    .cldci-pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
    }
    
    .cldci-pagination .page-item.active .page-link {
        background-color: #6366f1;
        border-color: #6366f1;
        color: white;
    }
    
    .cldci-pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
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
    }
    
    /* Estilos para Paginación Completa */
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        margin: 0 0.125rem;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #495057;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        min-width: 2.5rem;
        height: 2.5rem;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
        transform: translateY(-1px);
    }
    
    .pagination-btn.active {
        background-color: #6366f1;
        border-color: #6366f1;
        color: #fff;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }
    
    .pagination-btn-nav {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }
    
    .pagination-btn-nav:hover:not(:disabled) {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    
    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 0.125rem;
        margin: 0 0.5rem;
    }
    
    .pagination-ellipsis {
        padding: 0.5rem 0.25rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .pagination-progress {
        margin-top: 1rem;
    }
    
    .pagination-progress .progress {
        background-color: #e9ecef;
        border-radius: 2px;
    }
    
    .pagination-progress .progress-bar {
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 2px;
        transition: width 0.3s ease;
    }
    
    /* Estilos para el modal de links generados */
    .link-item {
        transition: all 0.3s ease;
    }
    
    .link-item:hover {
        background: #e9ecef !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .link-item .form-control {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        background: #fff;
        border: 1px solid #dee2e6;
    }
    
    .link-item .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
    }
    
    .link-item .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    
    .link-item .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }
    
    .swal2-popup {
        font-family: inherit;
    }
    
    .swal2-html-container {
        text-align: left !important;
    }
    
    /* Animaciones para las tarjetas de estadísticas */
    .stats-card {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Hover effects para las tarjetas de estadísticas */
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    /* Gradiente superior para las tarjetas */
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 8px 8px 0 0;
    }
</style>
@endsection

@section('content')
<!-- Estadísticas -->
<div class="col-xxl-12">
    <div class="row">
        <div class="col-xxl col-sm-6">
            <x-miembros.stat-card
                title="Total Elecciones"
                :value="number_format($estadisticas['total_elecciones'])"
                icon="ri-bar-chart-line"
                background="bg-primary-subtle"
                icon-background="bg-primary"
            />
        </div>
        <div class="col-xxl col-sm-6">
            <x-miembros.stat-card
                title="Elecciones Activas"
                :value="number_format($estadisticas['elecciones_activas'])"
                icon="ri-checkbox-circle-line"
                background="bg-success-subtle"
                icon-background="bg-success"
            />
        </div>
        <div class="col-xxl col-sm-6">
            <x-miembros.stat-card
                title="Próximas Elecciones"
                :value="number_format($estadisticas['proximas_elecciones'])"
                icon="ri-calendar-event-line"
                background="bg-info-subtle"
                icon-background="bg-info"
            />
        </div>
        <div class="col-xxl col-sm-6">
            <x-miembros.stat-card
                title="Votos Totales"
                :value="number_format($estadisticas['votos_totales'])"
                icon="ri-heart-line"
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
    form-id="elecciones-filters-form"
    form-action="{{ route('elecciones.index') }}"
    clear-url="{{ route('elecciones.index') }}"
    submit-label="Buscar"
    clear-label="Limpiar"
    variant="default"
    :filters="[
        [
            'name' => 'buscar',
            'label' => 'Buscar',
            'type' => 'text',
            'placeholder' => 'Título, descripción o organización',
            'col' => 'col-md-4'
        ],
        [
            'name' => 'tipo',
            'label' => 'Tipo de Elección',
            'type' => 'select',
            'placeholder' => 'Todos los tipos',
            'col' => 'col-md-3',
            'options' => [
                'directiva' => 'Directiva',
                'especial' => 'Especial',
                'ordinaria' => 'Ordinaria',
                'extraordinaria' => 'Extraordinaria'
            ]
        ],
        [
            'name' => 'estado',
            'label' => 'Estado',
            'type' => 'select',
            'placeholder' => 'Todos los estados',
            'col' => 'col-md-3',
            'options' => [
                'programada' => 'Programada',
                'activa' => 'Activa',
                'en-curso' => 'En Curso',
                'cerrada' => 'Cerrada',
                'finalizada' => 'Finalizada'
            ]
        ],
        [
            'name' => 'organizacion_id',
            'label' => 'Organización',
            'type' => 'select',
            'placeholder' => 'Todas las organizaciones',
            'col' => 'col-md-2',
            'options' => collect($organizaciones ?? [])->pluck('nombre', 'id')->toArray()
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

<!-- Tabla de Elecciones -->
<div class="col-xxl-12">
    <div class="card shadow-sm">
        <x-elecciones.table-header />
        <div class="card-body table-body">
            <x-elecciones.elecciones-table :elecciones="$elecciones" />

            <!-- Paginación Mejorada CLDCI -->
            @if($elecciones->hasPages() || $elecciones->total() > 0)
            <div class="cldci-pagination-container">
                <div class="row align-items-center">
                    <!-- Información de resultados -->
                    <div class="col-md-6">
                        <div class="pagination-info">
                            <span class="text-muted">
                                Mostrando 
                                <strong>{{ $elecciones->firstItem() ?? 0 }}</strong> 
                                a 
                                <strong>{{ $elecciones->lastItem() ?? 0 }}</strong> 
                                de 
                                <strong>{{ $elecciones->total() }}</strong> 
                                elecciones
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
                                    {{ $elecciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                <i class="ri-skip-back-line"></i>
                            </button>
                            
                            <!-- Botón Página Anterior -->
                            <button type="button" 
                                    class="pagination-btn pagination-btn-nav" 
                                    onclick="cambiarPagina({{ $elecciones->currentPage() - 1 }})" 
                                    title="Página anterior" 
                                    {{ $elecciones->currentPage() <= 1 ? 'disabled' : '' }}>
                                <i class="ri-arrow-left-line"></i>
                            </button>

                            <!-- Números de página -->
                            <div class="pagination-numbers">
                                @php
                                    $currentPage = $elecciones->currentPage();
                                    $lastPage = $elecciones->lastPage();
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
                                    onclick="cambiarPagina({{ $elecciones->currentPage() + 1 }})" 
                                    title="Página siguiente" 
                                    {{ $elecciones->currentPage() >= $elecciones->lastPage() ? 'disabled' : '' }}>
                                <i class="ri-arrow-right-line"></i>
                            </button>
                            
                            <!-- Botón Última Página -->
                            <button type="button" 
                                    class="pagination-btn pagination-btn-nav" 
                                    onclick="cambiarPagina({{ $elecciones->lastPage() }})" 
                                    title="Última página" 
                                    {{ $elecciones->currentPage() >= $elecciones->lastPage() ? 'disabled' : '' }}>
                                <i class="ri-skip-forward-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Indicador de progreso -->
                <div class="pagination-progress mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" 
                             style="width: {{ ($elecciones->currentPage() / $elecciones->lastPage()) * 100 }}%"
                             role="progressbar">
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            Página {{ $elecciones->currentPage() }} de {{ $elecciones->lastPage() }}
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Función para actualizar el estado y conteo regresivo de las elecciones
    function actualizarEstadoElecciones() {
        document.querySelectorAll('.estado-eleccion').forEach(elemento => {
            const eleccionId = elemento.dataset.eleccionId;
            const fechaInicio = new Date(elemento.dataset.fechaInicio);
            const fechaFin = new Date(elemento.dataset.fechaFin);
            const estadoActual = elemento.dataset.estadoActual;
            const ahora = new Date();
            
            const badge = document.getElementById(`badge-estado-${eleccionId}`);
            const countdown = document.getElementById(`countdown-${eleccionId}`);
            const timerText = document.getElementById(`timer-text-${eleccionId}`);
            
            let nuevoEstado = estadoActual;
            let mostrarCountdown = false;
            let tiempoRestante = '';
            
            // Determinar el estado actual basado en las fechas
            if (ahora < fechaInicio) {
                // Elección programada - mostrar tiempo hasta inicio
                nuevoEstado = 'programada';
                mostrarCountdown = true;
                const diff = fechaInicio - ahora;
                tiempoRestante = formatearTiempo(diff, 'Inicia en: ');
            } else if (ahora >= fechaInicio && ahora < fechaFin) {
                // Elección en curso - mostrar tiempo restante
                nuevoEstado = 'en-curso';
                mostrarCountdown = true;
                const diff = fechaFin - ahora;
                tiempoRestante = formatearTiempo(diff, 'Termina en: ');
                
                // Cambiar clase del countdown según urgencia
                countdown.className = 'countdown-timer mt-1';
                if (diff < 5 * 60 * 1000) { // Menos de 5 minutos
                    countdown.classList.add('urgente');
                } else if (diff < 15 * 60 * 1000) { // Menos de 15 minutos
                    countdown.classList.add('finalizando');
                }
            } else {
                // Elección cerrada
                nuevoEstado = 'cerrada';
                mostrarCountdown = false;
            }
            
            // Actualizar badge si el estado cambió
            if (nuevoEstado !== estadoActual) {
                badge.className = `eleccion-badge ${nuevoEstado}`;
                badge.textContent = nuevoEstado === 'en-curso' ? 'En Curso' : 
                                  nuevoEstado === 'cerrada' ? 'Cerrada' : 
                                  nuevoEstado === 'programada' ? 'Programada' : 
                                  ucfirst(nuevoEstado);
                elemento.dataset.estadoActual = nuevoEstado;
            }
            
            // Mostrar/ocultar countdown
            if (mostrarCountdown) {
                countdown.style.display = 'block';
                timerText.textContent = tiempoRestante;
            } else {
                countdown.style.display = 'none';
            }
        });
    }
    
    // Función para formatear el tiempo restante
    function formatearTiempo(milliseconds, prefix = '') {
        const totalSeconds = Math.floor(milliseconds / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        let tiempo = prefix;
        
        if (days > 0) {
            tiempo += `${days}d ${hours}h ${minutes}m`;
        } else if (hours > 0) {
            tiempo += `${hours}h ${minutes}m ${seconds}s`;
        } else if (minutes > 0) {
            tiempo += `${minutes}m ${seconds}s`;
        } else {
            tiempo += `${seconds}s`;
        }
        
        return tiempo;
    }
    
    // Función para capitalizar la primera letra
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    // Actualizar estado cada segundo
    setInterval(actualizarEstadoElecciones, 1000);
    
    // Ejecutar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        actualizarEstadoElecciones();
    });
    
    // Funciones para acciones de la tabla
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const eleccionCheckboxes = document.querySelectorAll('.eleccion-checkbox');
        
        eleccionCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateSelectAllState();
    }
    
    function updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const eleccionCheckboxes = document.querySelectorAll('.eleccion-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.eleccion-checkbox:checked');
        
        if (checkedCheckboxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes.length === eleccionCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
    
    function exportarElecciones() {
        const checkedCheckboxes = document.querySelectorAll('.eleccion-checkbox:checked');
        if (checkedCheckboxes.length === 0) {
            Swal.fire({
                title: 'Sin selección',
                text: 'Por favor selecciona al menos una elección para exportar.',
                icon: 'warning'
            });
            return;
        }
        
        Swal.fire({
            title: 'Exportando...',
            text: 'Preparando la exportación de elecciones.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Simular exportación
        setTimeout(() => {
            Swal.fire({
                title: 'Exportación completada',
                text: `Se exportaron ${checkedCheckboxes.length} elecciones exitosamente.`,
                icon: 'success'
            });
        }, 2000);
    }
    
    function imprimirLista() {
        const checkedCheckboxes = document.querySelectorAll('.eleccion-checkbox:checked');
        if (checkedCheckboxes.length === 0) {
            Swal.fire({
                title: 'Sin selección',
                text: 'Por favor selecciona al menos una elección para imprimir.',
                icon: 'warning'
            });
            return;
        }
        
        Swal.fire({
            title: 'Imprimiendo...',
            text: 'Preparando la lista para imprimir.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Simular impresión
        setTimeout(() => {
            Swal.fire({
                title: 'Impresión completada',
                text: `Se enviaron ${checkedCheckboxes.length} elecciones a la impresora.`,
                icon: 'success'
            });
        }, 2000);
    }
    
    function bulkAction(action) {
        const checkedCheckboxes = document.querySelectorAll('.eleccion-checkbox:checked');
        if (checkedCheckboxes.length === 0) {
            Swal.fire({
                title: 'Sin selección',
                text: 'Por favor selecciona al menos una elección.',
                icon: 'warning'
            });
            return;
        }
        
        let actionText = '';
        let confirmText = '';
        
        switch(action) {
            case 'activate':
                actionText = 'activar';
                confirmText = '¿Estás seguro de que quieres activar las elecciones seleccionadas?';
                break;
            case 'deactivate':
                actionText = 'desactivar';
                confirmText = '¿Estás seguro de que quieres desactivar las elecciones seleccionadas?';
                break;
            case 'delete':
                actionText = 'eliminar';
                confirmText = '¿Estás seguro de que quieres eliminar las elecciones seleccionadas? Esta acción no se puede deshacer.';
                break;
        }
        
        Swal.fire({
            title: `¿${ucfirst(actionText)} elecciones?`,
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${actionText}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: `${ucfirst(actionText)}...`,
                    text: `Procesando ${checkedCheckboxes.length} elecciones.`,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Simular acción masiva
                setTimeout(() => {
                    Swal.fire({
                        title: 'Acción completada',
                        text: `Se ${actionText}ron ${checkedCheckboxes.length} elecciones exitosamente.`,
                        icon: 'success'
                    });
                }, 2000);
            }
        });
    }
    
    function generarLinks(eleccionId, titulo) {
        Swal.fire({
            title: 'Generar Links de Votación',
            text: `¿Deseas generar links de votación para "${titulo}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generando links...',
                    text: 'Preparando los links de votación.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Simular generación de links
                setTimeout(() => {
                    mostrarLinksGenerados(eleccionId, titulo);
                }, 2000);
            }
        });
    }
    
    function mostrarLinksGenerados(eleccionId, titulo) {
        // Generar tokens únicos desde el servidor
        const baseUrl = window.location.origin;
        
        // Generar token público
        fetch(`${baseUrl}/api/elecciones/${eleccionId}/generar-token-publico`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const linksGenerados = [
                        {
                            tipo: 'Público',
                            url: data.url,
                            descripcion: 'Link público - Acceso directo para votar',
                            expira: data.expires_at
                        }
                    ];
                    
                    mostrarModalLinks(linksGenerados, titulo);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje,
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al generar el token de votación',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            });
    }
    
    function mostrarModalLinks(linksGenerados, titulo) {
        
        const linksHtml = linksGenerados.map((link, index) => `
            <div class="link-item mb-3 p-3 border rounded" style="background: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <i class="ri-link me-1"></i>
                            Link ${link.tipo}
                        </h6>
                        <small class="text-muted">${link.descripcion}</small>
                        <br>
                        <small class="text-warning">
                            <i class="ri-time-line me-1"></i>
                            Expira: ${new Date(link.expira).toLocaleString()}
                        </small>
                    </div>
                    <span class="badge ${link.tipo === 'Público' ? 'bg-success' : 'bg-primary'}">${link.tipo}</span>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" value="${link.url}" readonly id="link-${index}">
                    <button class="btn btn-outline-secondary" type="button" onclick="copiarLink(${index})" title="Copiar link">
                        <i class="ri-file-copy-line"></i>
                    </button>
                </div>
            </div>
        `).join('');
        
        Swal.fire({
            title: 'Links generados exitosamente',
            html: `
                <div class="text-start">
                    <p class="mb-3">Los siguientes links de votación han sido generados para <strong>"${titulo}"</strong>:</p>
                    ${linksHtml}
                    <div class="mt-3 p-2 bg-warning-subtle rounded">
                        <small class="text-warning">
                            <i class="ri-information-line me-1"></i>
                            <strong>Importante:</strong> Los links son únicos, tienen tiempo de expiración limitado y solo pueden usarse una vez.
                        </small>
                    </div>
                </div>
            `,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: 'Cerrar',
            cancelButtonText: 'Generar más',
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#198754',
            didOpen: () => {
                // Agregar estilos adicionales
                const swalContent = document.querySelector('.swal2-html-container');
                if (swalContent) {
                    swalContent.style.textAlign = 'left';
                }
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // Si el usuario quiere generar más links
                generarLinks(eleccionId, titulo);
            }
        });
    }
    
    function copiarLink(index) {
        const linkInput = document.getElementById(`link-${index}`);
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // Para móviles
        
        try {
            document.execCommand('copy');
            Swal.fire({
                title: '¡Copiado!',
                text: 'El link ha sido copiado al portapapeles.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (err) {
            // Fallback para navegadores modernos
            navigator.clipboard.writeText(linkInput.value).then(() => {
                Swal.fire({
                    title: '¡Copiado!',
                    text: 'El link ha sido copiado al portapapeles.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo copiar el link. Por favor, cópialo manualmente.',
                    icon: 'error'
                });
            });
        }
    }
    
    function eliminarEleccion(eleccionId) {
        Swal.fire({
            title: '¿Eliminar elección?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'La elección está siendo eliminada.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Simular eliminación
                setTimeout(() => {
                    Swal.fire({
                        title: 'Eliminada',
                        text: 'La elección ha sido eliminada exitosamente.',
                        icon: 'success'
                    });
                }, 2000);
            }
        });
    }
    
    function verCandidatos(eleccionId, titulo) {
        Swal.fire({
            title: 'Ver Candidatos',
            text: `Mostrando candidatos para "${titulo}"`,
            icon: 'info',
            confirmButtonText: 'Ver candidatos'
        }).then(() => {
            window.location.href = '{{ route("elecciones.candidatos") }}';
        });
    }
    
    // Funciones para Controles de Vista y Paginación
    function changePageSize(value) {
        const url = new URL(window.location);
        if (value === 'all') {
            url.searchParams.delete('per_page');
        } else {
            url.searchParams.set('per_page', value);
        }
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }
    
    function cambiarPagina(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
    
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Actualizar estado de elecciones al cargar
        actualizarEstadoElecciones();
    });
</script>
@endsection