@extends('partials.layouts.master')

@section('title', 'Perfil de Miembro | CLDCI')
@section('title-sub', 'Miembros')
@section('pagetitle', 'Perfil de Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/qrcode/qrcode.min.css') }}">
<style>
    /* Estilos para la card de Cargos Actuales */
    .cargo-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(var(--bs-primary-rgb), 0.1) !important;
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.02) 0%, rgba(var(--bs-primary-rgb), 0.05) 100%) !important;
    }
    
    .cargo-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb), 0.15);
        border-color: rgba(var(--bs-primary-rgb), 0.2) !important;
    }
    
    .cargo-item .avatar-lg {
        width: 3rem;
        height: 3rem;
        transition: all 0.3s ease;
    }
    
    .cargo-item:hover .avatar-lg {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.3);
    }
    
    .cargo-item .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .cargo-item .progress {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-radius: 0 0 0.5rem 0.5rem;
    }
    
    .cargo-item .progress-bar {
        background: linear-gradient(90deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
        border-radius: 0 0 0.5rem 0.5rem;
    }
    
    /* Animación para el estado vacío */
    .avatar-xl {
        width: 4rem;
        height: 4rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Mejoras para el header de la card */
    .card-header .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 1rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .cargo-item {
            margin-bottom: 1rem;
        }
        
        .cargo-item .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .cargo-item .ms-3 {
            margin-left: 0 !important;
            margin-top: 1rem;
        }
    }
</style>
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
<div class="pages-profile">       
</div>

<div class="main-profile-bg position-relative">
    <div class="profile-bg">
        <img src="{{ asset('assets/images/p-bg.jpg') }}" alt="Profile Background" class="w-100 h-100 object-fit-cover">
    </div>
</div>
<div class="position-relative z-1 text-end edit-btn">
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('carnet.selector', $miembro->id) }}" class="btn btn-primary">
            <i class="ri-qr-code-line me-1"></i> Carnet Digital
        </a>
        <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn border border-white text-white">
            <i class="ri-edit-line me-1"></i> Editar Perfil
        </a>
    </div>
</div>
<div class="card overflow-hidden position-relative z-1">
    <div class="card-body p-5">
        <div class="d-flex justify-content-between flex-wrap align-items-center gap-6">
            <div class="flex-shrink-0">
                <div class="position-relative d-inline-block">
                    @if($miembro->foto_url)
                        <img src="{{ asset('storage/' . $miembro->foto_url) }}" alt="Avatar Image" class="h-100px w-100px rounded-pill">
                    @else
                        <div class="h-100px w-100px rounded-pill bg-primary d-flex justify-content-center align-items-center text-white fs-2">
                            {{ substr($miembro->nombre, 0, 1) }}{{ substr($miembro->apellido, 0, 1) }}
                        </div>
                    @endif
                    <div class="h-30px w-30px rounded-pill bg-primary d-flex justify-content-center align-items-center text-white border border-3 border-light-subtle position-absolute fs-12 bottom-0 end-0">
                        <i class="bi bi-camera"></i>
                    </div>
                    <span class="position-absolute profile-dot bg-success rounded-circle">
                        <span class="visually-hidden">miembro activo</span>
                    </span>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1">
                    {{ $miembro->nombre }} {{ $miembro->apellido }}
                    @if($miembro->tipo_membresia == 'fundador')
                        <i class="bi bi-patch-check-fill fs-16 ms-1 text-warning"></i>
                    @elseif($miembro->tipo_membresia == 'activo')
                        <i class="bi bi-patch-check-fill fs-16 ms-1 text-success"></i>
                    @endif
                </h4>
                <p class="text-muted mb-1">{{ ucfirst($miembro->tipo_membresia) }} - {{ $miembro->profesion ?? 'Profesional' }}</p>
                <p class="text-muted mb-0">{{ $miembro->organizacion->nombre }}</p>
            </div>
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['asambleas_asistidas'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Asambleas</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['capacitaciones_inscrito'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Capacitaciones</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['años_membresia'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Años Miembro</span>
                </div>
            </div>
            <div class="d-flex float-end gap-2 flex-shrink-0">
                <a href="{{ route('miembros.carnet', $miembro->id) }}" class="btn btn-light">
                    <i class="ri-qr-code-line me-1"></i> Carnet Digital
                </a>
                <a href="{{ route('miembros.index') }}" class="btn btn-primary">
                    <i class="ri-arrow-left-line me-1"></i> Volver a Miembros
                </a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Personal</h5>
                <a href="{{ route('miembros.edit', $miembro->id) }}" class="fs-14"><i class="ri-edit-line me-1"></i>Editar</a>
            </div>
            <div class="card-body d-flex flex-column gap-4 text-truncate">
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-map-pin-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ $miembro->organizacion->nombre }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-mail-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ $miembro->email }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-phone-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ $miembro->telefono ?? 'No especificado' }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-shield-user-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ ucfirst($miembro->tipo_membresia) }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-user-2-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ ucfirst($miembro->estado_membresia) }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-calendar-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ $estadisticas['años_membresia'] }} años de membresía</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-time-line fs-16 text-muted"></i>
                    <p class="mb-0">Miembro desde {{ $miembro->fecha_ingreso->format('M d, Y') }}</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="ri-id-card-line fs-16 text-muted"></i>
                    <p class="mb-0">{{ $miembro->numero_carnet }}</p>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Participación CLDCI</h5>
            </div>
            <div class="card-body d-flex flex-column gap-4">
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="text-truncate"><i class="ri-calendar-line me-2 text-muted"></i>Asambleas</span>
                        <span class="text-muted">{{ $estadisticas['asambleas_asistidas'] }}</span>
                    </div>
                    <div class="progress progress-sm" role="progressbar" aria-label="Asambleas" aria-valuenow="{{ $estadisticas['asambleas_asistidas'] }}" aria-valuemin="0" aria-valuemax="10">
                        <div class="progress-bar" style="width: {{ min(($estadisticas['asambleas_asistidas'] / 10) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="text-truncate"><i class="ri-graduation-cap-line me-2 text-muted"></i>Capacitaciones</span>
                        <span class="text-muted">{{ $estadisticas['capacitaciones_inscrito'] }}</span>
                    </div>
                    <div class="progress progress-sm" role="progressbar" aria-label="Capacitaciones" aria-valuenow="{{ $estadisticas['capacitaciones_inscrito'] }}" aria-valuemin="0" aria-valuemax="5">
                        <div class="progress-bar bg-success" style="width: {{ min(($estadisticas['capacitaciones_inscrito'] / 5) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="text-truncate"><i class="ri-government-line me-2 text-muted"></i>Elecciones</span>
                        <span class="text-muted">{{ $estadisticas['elecciones_participado'] }}</span>
                    </div>
                    <div class="progress progress-sm" role="progressbar" aria-label="Elecciones" aria-valuenow="{{ $estadisticas['elecciones_participado'] }}" aria-valuemin="0" aria-valuemax="3">
                        <div class="progress-bar bg-warning" style="width: {{ min(($estadisticas['elecciones_participado'] / 3) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="text-truncate"><i class="ri-time-line me-2 text-muted"></i>Antigüedad</span>
                        <span class="text-muted">{{ $estadisticas['años_membresia'] }} años</span>
                    </div>
                    <div class="progress progress-sm" role="progressbar" aria-label="Antigüedad" aria-valuenow="{{ $estadisticas['años_membresia'] }}" aria-valuemin="0" aria-valuemax="10">
                        <div class="progress-bar bg-info" style="width: {{ min(($estadisticas['años_membresia'] / 10) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="ri-qr-code-line me-2 text-primary"></i>
                    Carnet Digital
                </h5>
                <span class="badge bg-primary-subtle text-primary">
                    <i class="ri-check-line me-1"></i>Activo
                </span>
            </div>
            <div class="card-body">
                <!-- Información del carnet -->
                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                        <i class="ri-id-card-line fs-24"></i>
                    </div>
                    <h6 class="mb-1">{{ $miembro->numero_carnet }}</h6>
                    <p class="text-muted mb-0 fs-12">Número de Carnet</p>
                </div>
                
                <!-- Botones de acción estratégicamente posicionados -->
                <div class="d-grid gap-2">
                    <!-- Botón principal - Ver Carnet -->
                    <a href="{{ route('carnet.selector', $miembro->id) }}" class="btn btn-primary d-flex align-items-center justify-content-center">
                        <i class="ri-eye-line me-2"></i>
                        Ver Carnet Digital
                    </a>
                    
                    <!-- Botones secundarios en fila -->
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('carnet.selector', $miembro->id) }}" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center" title="Descargar PDF">
                                <i class="ri-download-line fs-16"></i>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('carnet.selector', $miembro->id) }}" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center" title="Imprimir">
                                <i class="ri-printer-line fs-16"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ri-calendar-line me-1"></i>
                                Válido hasta
                            </small>
                            <small class="fw-medium text-primary">
                                {{ $miembro->fecha_ingreso->addYears(2)->format('M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 order-last order-xl-2">
        <div class="tab-content">
            <div class="tab-pane active show" id="pages-profile-tab" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Información del Miembro</h5>
                        <span class="badge bg-{{ $miembro->estado_membresia == 'activa' ? 'success' : 'warning' }}-subtle text-{{ $miembro->estado_membresia == 'activa' ? 'success' : 'warning' }}">
                            {{ ucfirst($miembro->estado_membresia) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>{{ $miembro->nombre }} {{ $miembro->apellido }}</strong> es un miembro {{ $miembro->tipo_membresia }} de la {{ $miembro->organizacion->nombre }}. 
                            Su participación activa en las actividades de la organización demuestra su compromiso con los valores y objetivos de CLDCI.
                        </p>
                        <p class="mb-0">
                            Como {{ $miembro->tipo_membresia }}, {{ $miembro->nombre }} ha contribuido significativamente al desarrollo de la organización a través de su participación en asambleas, capacitaciones y procesos electorales. Su dedicación y profesionalismo son un ejemplo para otros miembros.
                        </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Actividad Reciente</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline2 icon-timeline">
                            <ul>
                                @foreach($actividadReciente as $actividad)
                                <li class="box">
                                    <span class="bg-{{ $actividad['color'] }}">
                                        <i class="{{ $actividad['icono'] }}"></i>
                                    </span>
                                    <div class="text-muted float-end fs-13">{{ $actividad['fecha']->format('d M Y') }}</div>
                                    <div class="title">{{ $actividad['titulo'] }}</div>
                                    <div class="info">{{ $actividad['descripcion'] }}</div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="pages-projects-tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Participación en Actividades</h5>
                        <div class="mb-5 pb-5 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Asambleas Generales</h6>
                                <span class="badge bg-primary">Participante Activo</span>
                            </div>
                            <p class="text-muted mb-3">Total Asistidas: <span class="text-body">{{ $estadisticas['asambleas_asistidas'] }}</span></p>
                            <p>El miembro ha demostrado un compromiso constante con la participación democrática en las asambleas generales de la organización.</p>
                        </div>
                        <div class="mb-5 pb-5 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Capacitaciones</h6>
                                <span class="badge bg-success">En Progreso</span>
                            </div>
                            <p class="text-muted mb-3">Total Inscrito: <span class="text-body">{{ $estadisticas['capacitaciones_inscrito'] }}</span></p>
                            <p>Participación activa en programas de formación y desarrollo profesional ofrecidos por la organización.</p>
                        </div>
                        <div class="mb-5 pb-5 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Procesos Electorales</h6>
                                <span class="badge bg-info">Participante</span>
                            </div>
                            <p class="text-muted mb-3">Total Participado: <span class="text-body">{{ $estadisticas['elecciones_participado'] }}</span></p>
                            <p>Ejercicio responsable del derecho al voto en los procesos democráticos de la organización.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="pages-details-tab" role="tabpanel">
                <div class="row">
                    <!-- Historial de Asambleas -->
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Historial de Asambleas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Asamblea</th>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Asistencia</th>
                                                <th>Modalidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($asambleasHistorial ?? [] as $asamblea)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2">
                                                            <i class="ri-calendar-line fs-16"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $asamblea['titulo'] ?? 'Asamblea General' }}</h6>
                                                            <small class="text-muted">{{ $asamblea['descripcion'] ?? 'Descripción no disponible' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $asamblea['fecha'] ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $asamblea['tipo'] == 'ordinaria' ? 'primary' : ($asamblea['tipo'] == 'extraordinaria' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($asamblea['tipo'] ?? 'ordinaria') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $asamblea['presente'] ? 'success' : 'secondary' }}">
                                                        {{ $asamblea['presente'] ? 'Presente' : 'Ausente' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $asamblea['modalidad'] == 'presencial' ? 'primary' : ($asamblea['modalidad'] == 'virtual' ? 'info' : 'warning') }}">
                                                        {{ ucfirst($asamblea['modalidad'] ?? 'presencial') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="avatar-lg mx-auto mb-3">
                                                        <div class="avatar-title bg-light text-muted rounded-circle">
                                                            <i class="ri-calendar-line fs-24"></i>
                                                        </div>
                                                    </div>
                                                    <h6 class="text-muted">Sin historial de asambleas</h6>
                                                    <p class="text-muted mb-0 fs-12">Este miembro no tiene registros de participación en asambleas.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Cursos Inscritos -->
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Cursos Inscritos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Curso</th>
                                                <th>Fecha Inicio</th>
                                                <th>Modalidad</th>
                                                <th>Estado</th>
                                                <th>Calificación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($cursosInscritos ?? [] as $curso)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-2">
                                                            <i class="ri-graduation-cap-line fs-16"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $curso['titulo'] ?? 'Curso de Capacitación' }}</h6>
                                                            <small class="text-muted">{{ $curso['instructor'] ?? 'Instructor no especificado' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $curso['fecha_inicio'] ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $curso['modalidad'] == 'presencial' ? 'primary' : ($curso['modalidad'] == 'virtual' ? 'info' : 'warning') }}">
                                                        {{ ucfirst($curso['modalidad'] ?? 'presencial') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $curso['estado'] == 'completado' ? 'success' : ($curso['estado'] == 'inscrito' ? 'primary' : 'secondary') }}">
                                                        {{ ucfirst($curso['estado'] ?? 'inscrito') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($curso['calificacion'])
                                                        <span class="badge bg-success">{{ $curso['calificacion'] }}%</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="avatar-lg mx-auto mb-3">
                                                        <div class="avatar-title bg-light text-muted rounded-circle">
                                                            <i class="ri-graduation-cap-line fs-24"></i>
                                                        </div>
                                                    </div>
                                                    <h6 class="text-muted">Sin cursos inscritos</h6>
                                                    <p class="text-muted mb-0 fs-12">Este miembro no tiene inscripciones en cursos actualmente.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="pages-settings-tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('miembros.update', $miembro->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $miembro->nombre }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" value="{{ $miembro->apellido }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $miembro->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ $miembro->telefono }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="profesion" class="form-label">Profesión</label>
                                    <input type="text" class="form-control" id="profesion" name="profesion" value="{{ $miembro->profesion }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_membresia" class="form-label">Tipo de Membresía</label>
                                    <select class="form-select" id="tipo_membresia" name="tipo_membresia">
                                        <option value="fundador" {{ $miembro->tipo_membresia == 'fundador' ? 'selected' : '' }}>Fundador</option>
                                        <option value="activo" {{ $miembro->tipo_membresia == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="pasivo" {{ $miembro->tipo_membresia == 'pasivo' ? 'selected' : '' }}>Pasivo</option>
                                        <option value="honorifico" {{ $miembro->tipo_membresia == 'honorifico' ? 'selected' : '' }}>Honorífico</option>
                                        <option value="estudiante" {{ $miembro->tipo_membresia == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                        <option value="diaspora" {{ $miembro->tipo_membresia == 'diaspora' ? 'selected' : '' }}>Diáspora</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="estado_membresia" class="form-label">Estado de Membresía</label>
                                    <select class="form-select" id="estado_membresia" name="estado_membresia">
                                        <option value="activa" {{ $miembro->estado_membresia == 'activa' ? 'selected' : '' }}>Activa</option>
                                        <option value="suspendida" {{ $miembro->estado_membresia == 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                        <option value="inactiva" {{ $miembro->estado_membresia == 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                        <option value="honoraria" {{ $miembro->estado_membresia == 'honoraria' ? 'selected' : '' }}>Honoraria</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="{{ $miembro->fecha_ingreso->format('Y-m-d') }}">
                                </div>
                                <div class="col-12">
                                    <label for="foto" class="form-label">Foto de Perfil</label>
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 order-2 order-xl-last">
        <div class="d-flex flex-column">
            <div class="card order-2 order-xl-1">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pages-profile-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-user-line me-3"></i>Perfil</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-projects-tab" role="tab" aria-selected="true">
                                <div class="fw-semibold"><i class="ri-calendar-line me-3"></i>Actividades</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-details-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-file-list-3-line me-3"></i>Detalles</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-settings-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-settings-3-line me-3"></i>Configuración</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card order-1 order-xl-2">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-star-line me-2 text-primary"></i>
                        Cargos Actuales
                    </h5>
                    <span class="badge bg-primary-subtle text-primary">{{ count($cargosActuales) }} cargo(s)</span>
                </div>
                <div class="card-body">
                    @if(count($cargosActuales) > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($cargosActuales as $index => $cargo)
                        <div class="cargo-item p-3 border rounded-3 bg-light-subtle position-relative overflow-hidden">
                            <!-- Decoración de fondo -->
                            <div class="position-absolute top-0 end-0 opacity-10">
                                <i class="ri-user-star-line" style="font-size: 3rem; color: var(--bs-primary);"></i>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <!-- Avatar con número de cargo -->
                                <div class="flex-shrink-0 position-relative">
                                    <div class="avatar-lg rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center shadow-sm">
                                        <i class="ri-user-line fs-18"></i>
                                    </div>
                                    <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                
                                <!-- Contenido del cargo -->
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="mb-0 fw-semibold text-dark">
                                            {{ $cargo['cargo'] ?? 'Cargo no especificado' }}
                                        </h6>
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="ri-check-line me-1"></i>Activo
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center text-muted mb-1">
                                            <i class="ri-building-line me-2 fs-14"></i>
                                            <span class="fw-medium">{{ $cargo['organo'] ?? 'Órgano no especificado' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="ri-calendar-line me-2 fs-14"></i>
                                            <small class="fw-medium">Desde {{ $cargo['fecha_inicio'] ?? 'N/A' }}</small>
                                        </div>
                                        
                                        <!-- Indicador de duración -->
                                        <div class="text-end">
                                            <small class="text-muted">
                                                @php
                                                    $fechaInicio = $cargo['fecha_inicio'] ?? null;
                                                    if ($fechaInicio) {
                                                        $inicio = \Carbon\Carbon::parse($fechaInicio);
                                                        $duracion = $inicio->diffInMonths(now());
                                                        echo $duracion . ' mes' . ($duracion != 1 ? 'es' : '');
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                @endphp
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso decorativa -->
                            <div class="position-absolute bottom-0 start-0 w-100">
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar bg-primary" style="width: {{ rand(60, 100) }}%;"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="avatar-xl mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle shadow-sm">
                                <i class="ri-user-line fs-28"></i>
                            </div>
                        </div>
                        <h6 class="text-muted mb-2">Sin cargos directivos</h6>
                        <p class="text-muted mb-0 fs-14">Este miembro no tiene cargos directivos asignados actualmente.</p>
                        <div class="mt-3">
                            <span class="badge bg-light text-muted">
                                <i class="ri-information-line me-1"></i>
                                Información no disponible
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card order-1 order-xl-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-bar-chart-line me-2 text-primary"></i>
                        Estadísticas del Miembro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3 bg-primary-subtle">
                                <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-calendar-check-line fs-16"></i>
                                </div>
                                <h4 class="mb-1 text-primary">{{ $estadisticas['asambleas_asistidas'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Asambleas</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3 bg-success-subtle">
                                <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-graduation-cap-line fs-16"></i>
                                </div>
                                <h4 class="mb-1 text-success">{{ $estadisticas['cursos_completados'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Cursos</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3 bg-info-subtle">
                                <div class="avatar-sm rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-vote-line fs-16"></i>
                                </div>
                                <h4 class="mb-1 text-info">{{ $estadisticas['votos_emitidos'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Votos</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded-3 bg-warning-subtle">
                                <div class="avatar-sm rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="ri-user-star-line fs-16"></i>
                                </div>
                                <h4 class="mb-1 text-warning">{{ $estadisticas['cargos_actuales'] }}</h4>
                                <p class="text-muted mb-0 fs-12">Cargos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Carnet Digital -->
@include('miembros.partials.carnet-modal')
@endsection

@section('js')
<!-- Uploaded js -->
<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
<!-- Picker -->
<script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>
<!-- Select -->
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<!-- Upload -->
<script src="{{ asset('assets/js/form/file-upload.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/profile.init.js') }}"></script>
<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- JavaScript del Modal del Carnet -->
<script>
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
