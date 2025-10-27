@extends('partials.layouts.master')

@section('title', 'Gestión Documental | CLDCI')
@section('title-sub', 'Centro de Documentos')
@section('pagetitle', 'Gestión Documental')

@section('css')
<style>
    .stat-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .recent-doc-item {
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .recent-doc-item:hover {
        background: #f8f9fa;
        border-color: #0d6efd;
    }
    
    .quick-action-btn {
        height: 120px;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }
    
    .quick-action-btn:hover {
        border-color: #0d6efd;
        background: rgba(13, 110, 253, 0.05);
        transform: translateY(-3px);
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Estadísticas principales -->
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xxl-3 col-sm-6">
                <div class="card stat-card bg-primary-subtle border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary rounded-circle fs-3">
                                    <i class="ri-folder-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-2">Total Documentos</p>
                                <h4 class="mb-0">1,234</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-sm-6">
                <div class="card stat-card bg-success-subtle border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success rounded-circle fs-3">
                                    <i class="ri-share-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-2">Compartidos</p>
                                <h4 class="mb-0">156</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-sm-6">
                <div class="card stat-card bg-warning-subtle border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-circle fs-3">
                                    <i class="ri-time-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-2">Pendientes Aprobar</p>
                                <h4 class="mb-0">12</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-sm-6">
                <div class="card stat-card bg-danger-subtle border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger rounded-circle fs-3">
                                    <i class="ri-edit-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-2">Pendientes Firmar</p>
                                <h4 class="mb-0">5</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-flashlight-line me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('gestion-documental.documentos.create') }}" 
                           class="btn quick-action-btn w-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="ri-upload-cloud-line fs-1 text-primary mb-2"></i>
                            <span class="fw-semibold">Subir Documento</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('gestion-documental.carpetas.create') }}" 
                           class="btn quick-action-btn w-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="ri-folder-add-line fs-1 text-success mb-2"></i>
                            <span class="fw-semibold">Nueva Carpeta</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('gestion-documental.busqueda.index') }}" 
                           class="btn quick-action-btn w-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="ri-search-line fs-1 text-info mb-2"></i>
                            <span class="fw-semibold">Buscar Documentos</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('gestion-documental.secciones.index') }}" 
                           class="btn quick-action-btn w-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="ri-folders-line fs-1 text-warning mb-2"></i>
                            <span class="fw-semibold">Ver Secciones</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos recientes y pendientes -->
    <div class="col-xxl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="ri-file-list-line me-2"></i>
                    Documentos Recientes
                </h5>
                <a href="{{ route('gestion-documental.documentos.index') }}" class="btn btn-sm btn-primary">
                    Ver Todos
                </a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="recent-doc-item mb-2">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded">
                                    <i class="ri-file-pdf-line fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Documento Ejemplo {{ $i }}.pdf</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="ri-folder-line me-1"></i>
                                    Contratos / Clientes
                                    <span class="mx-2">•</span>
                                    <i class="ri-calendar-line me-1"></i>
                                    Hace 2 horas
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-soft-primary">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-success">
                                    <i class="ri-download-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Pendientes y actividad -->
    <div class="col-xxl-4">
        <!-- Mis Pendientes -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-task-line me-2"></i>
                    Mis Pendientes
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('gestion-documental.aprobaciones.mis-pendientes') }}" 
                       class="btn btn-soft-warning w-100 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="ri-checkbox-circle-line me-2"></i>
                            Aprobaciones
                        </span>
                        <span class="badge bg-warning">12</span>
                    </a>
                </div>
                <div class="mb-3">
                    <a href="{{ route('gestion-documental.firmas.mis-pendientes') }}" 
                       class="btn btn-soft-danger w-100 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="ri-edit-line me-2"></i>
                            Firmas
                        </span>
                        <span class="badge bg-danger">5</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-history-line me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @for($i = 1; $i <= 4; $i++)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="avatar-xs flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="ri-upload-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fs-14">Documento subido</h6>
                                <p class="text-muted mb-0 small">Hace {{ $i }} horas</p>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Actualizar estadísticas en tiempo real
    function actualizarEstadisticas() {
        // TODO: Implementar con AJAX
        console.log('Actualizando estadísticas...');
    }

    // Actualizar cada 30 segundos
    setInterval(actualizarEstadisticas, 30000);
</script>
@endsection

