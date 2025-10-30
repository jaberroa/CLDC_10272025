@extends('partials.layouts.master')

@section('title', 'Perfil de Organización | CLDCI')
@section('title-sub', 'Gestión de Organizaciones')
@section('pagetitle', 'Perfil de Organización')

@section('css')
<link rel="stylesheet" href="{{ vite_asset('resources/css/organizaciones/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <!-- Header del perfil -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-4">
                                @if($organizacion->logo_url)
                                    <img src="{{ asset($organizacion->logo_url) }}" 
                                         alt="{{ $organizacion->nombre }}" 
                                         class="avatar-xl rounded">
                                @else
                                    <div class="avatar-xl bg-primary-subtle rounded d-flex align-items-center justify-content-center">
                                        <i class="ri-building-line text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1">{{ $organizacion->nombre }}</h2>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-secondary me-2">{{ $organizacion->codigo }}</span>
                                    <x-organizaciones.tipo-badge :tipo="$organizacion->tipo" />
                                    <x-organizaciones.estado-badge :estado="$organizacion->estado" class="ms-2" />
                                </div>
                                @if($organizacion->descripcion)
                                    <p class="text-muted mb-0">{{ $organizacion->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-md-end">
                            <a href="{{ route('organizaciones.edit', $organizacion->id) }}" class="btn btn-primary me-2">
                                <i class="ri-edit-line"></i>
                                Editar
                            </a>
                            <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas principales -->
        <div class="row g-4 mb-4">
            <div class="col-xxl col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-primary-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-primary d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Total Miembros</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_miembros']) }}</h4>
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
                                <i class="ri-check-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Miembros Activos</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['miembros_activos']) }}</h4>
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
                                <i class="ri-calendar-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Asambleas</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_asambleas']) }}</h4>
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
                                <i class="ri-government-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Elecciones</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_elecciones']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información de contacto -->
            <div class="col-xxl-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-contacts-line"></i>
                            Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-map-pin-line text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Dirección</h6>
                                <p class="text-muted mb-0">
                                    {{ $organizacion->direccion ?? 'No especificada' }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-phone-line text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Teléfono</h6>
                                <p class="text-muted mb-0">
                                    {{ $organizacion->telefono ?? 'No especificado' }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-mail-line text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Email</h6>
                                <p class="text-muted mb-0">
                                    {{ $organizacion->email ?? 'No especificado' }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-calendar-line text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Fecha de Creación</h6>
                                <p class="text-muted mb-0">
                                    {{ $organizacion->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Miembros más activos -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-user-star-line"></i>
                            Miembros Destacados
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($miembrosActivos as $miembro)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ asset($miembro['foto_url']) }}" 
                                         alt="{{ $miembro['nombre'] }}" 
                                         class="avatar-sm rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $miembro['nombre'] }}</h6>
                                    <small class="text-muted">{{ $miembro['numero_carnet'] }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">{{ $miembro['años_membresia'] }} años</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="ri-user-line fs-1 mb-2"></i>
                                <p class="mb-0">No hay miembros registrados</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-xxl-8">
                <!-- Actividad reciente -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-time-line"></i>
                            Actividad Reciente
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($actividadReciente as $actividad)
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-{{ $actividad['color'] }}-subtle rounded d-flex align-items-center justify-content-center">
                                        <i class="{{ $actividad['icono'] }} text-{{ $actividad['color'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="fs-18 mb-0 me-2">{{ $actividad['titulo'] }}</h6>
                                        <small class="text-muted">{{ $actividad['fecha']->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 text-muted fs-14">{{ $actividad['descripcion'] }}</p>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-4">
                            @endif
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="ri-time-line fs-1 mb-3"></i>
                                <p class="mb-0">No hay actividad reciente</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Estadísticas detalladas -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-bar-chart-line"></i>
                            Estadísticas Detalladas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="text-primary mb-1">{{ $estadisticas['miembros_vencidos'] }}</h4>
                                    <p class="text-muted mb-0">Miembros Vencidos</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="text-info mb-1">{{ $estadisticas['asambleas_activas'] }}</h4>
                                    <p class="text-muted mb-0">Asambleas Activas</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="text-warning mb-1">{{ $estadisticas['elecciones_activas'] }}</h4>
                                    <p class="text-muted mb-0">Elecciones Activas</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="text-success mb-1">{{ $estadisticas['cursos_activos'] }}</h4>
                                    <p class="text-muted mb-0">Cursos Activos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ vite_asset('resources/js/organizaciones/app.js') }}"></script>
@endsection

