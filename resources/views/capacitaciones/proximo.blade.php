@extends('partials.layouts.master')

@section('title', 'Próximos Cursos | CLDCI')
@section('title-sub', 'Gestión de Capacitaciones')
@section('pagetitle', 'Próximos Cursos')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-create-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-body">
                @if($proximoCurso)
                    <!-- Estadísticas del próximo curso -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-2 col-md-4 col-sm-6">
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
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card border-0 bg-success-subtle">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                        <i class="ri-user-check-line text-white fs-4"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $estadisticas['inscripciones_confirmadas'] }}</h6>
                                    <p class="text-muted mb-0 small">Inscritos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card border-0 bg-info-subtle">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                        <i class="ri-group-line text-white fs-4"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $estadisticas['cupo_disponible'] }}</h6>
                                    <p class="text-muted mb-0 small">Cupos Disponibles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card border-0 bg-warning-subtle">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                        <i class="ri-calendar-line text-white fs-4"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $estadisticas['dias_restantes'] }}</h6>
                                    <p class="text-muted mb-0 small">Días Restantes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card border-0 bg-secondary-subtle">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                        <i class="ri-percent-line text-white fs-4"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $estadisticas['porcentaje_ocupacion'] }}%</h6>
                                    <p class="text-muted mb-0 small">Ocupación</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card border-0 bg-danger-subtle">
                                <div class="card-body text-center">
                                    <div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                        <i class="ri-money-dollar-circle-line text-white fs-4"></i>
                                    </div>
                                    <h6 class="mb-1">RD$ {{ number_format($proximoCurso->costo, 0) }}</h6>
                                    <p class="text-muted mb-0 small">Costo</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Header Próximos Cursos -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header miembros-create-header">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <a href="{{ route('capacitaciones.index') }}" class="btn btn-volver">
                                        <i class="ri-arrow-left-line"></i>
                                        <span>Volver</span>
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title">
                                        <i class="ri-graduation-cap-line"></i>
                                        Próximos Cursos
                                    </h4>
                                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                                        Información de los próximos cursos de capacitación programados
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('capacitaciones.create') }}" class="btn btn-outline-light btn-sm">
                                        <i class="ri-add-line me-1"></i> Nuevo Curso
                                    </a>
                                    <a href="{{ route('capacitaciones.index') }}" class="btn btn-outline-light btn-sm">
                                        <i class="ri-list-check me-1"></i> Todos los Cursos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del próximo curso -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-graduation-cap-line me-2"></i>
                                        {{ $proximoCurso->titulo }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Modalidad</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-{{ $proximoCurso->modalidad === 'presencial' ? 'building' : ($proximoCurso->modalidad === 'virtual' ? 'computer' : 'mix') }}-line text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ ucfirst($proximoCurso->modalidad) }}</h6>
                                                    <small class="text-muted">Modalidad de enseñanza</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Instructor</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-user-line text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $proximoCurso->instructor }}</h6>
                                                    <small class="text-muted">Instructor Principal</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Fecha de Inicio</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-calendar-line text-info"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $proximoCurso->fecha_inicio->format('d/m/Y') }}</h6>
                                                    <small class="text-muted">{{ $proximoCurso->fecha_inicio->format('H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        @if($proximoCurso->fecha_fin)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Fecha de Finalización</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-calendar-check-line text-warning"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $proximoCurso->fecha_fin->format('d/m/Y') }}</h6>
                                                    <small class="text-muted">{{ $proximoCurso->fecha_fin->format('H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Lugar</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-map-pin-line text-secondary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $proximoCurso->lugar }}</h6>
                                                    <small class="text-muted">Ubicación del curso</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Modalidad</label>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ri-{{ $proximoCurso->modalidad === 'presencial' ? 'building' : ($proximoCurso->modalidad === 'virtual' ? 'computer' : 'mix') }}-line text-danger"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ ucfirst($proximoCurso->modalidad) }}</h6>
                                                    <small class="text-muted">Modalidad de enseñanza</small>
                                                </div>
                                            </div>
                                        </div>

                                        @if($proximoCurso->descripcion)
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Descripción</label>
                                            <div class="p-3 bg-light rounded">
                                                {{ $proximoCurso->descripcion }}
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Progreso de Inscripciones</h6>
                                                    <div class="progress" style="width: 200px;">
                                                        <div class="progress-bar bg-{{ $estadisticas['porcentaje_ocupacion'] > 80 ? 'danger' : ($estadisticas['porcentaje_ocupacion'] > 60 ? 'warning' : 'success') }}" 
                                                             style="width: {{ $estadisticas['porcentaje_ocupacion'] }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h6 class="mb-0">{{ $estadisticas['inscripciones_confirmadas'] }} / {{ $proximoCurso->cupo_maximo }}</h6>
                                                    <small class="text-muted">Inscritos / Cupo Total</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Mensaje cuando no hay próximo curso -->
                    <div class="text-center py-5">
                        <div class="avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                            <i class="ri-graduation-cap-line text-muted" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="text-muted">No hay cursos programados</h5>
                        <p class="text-muted">No se encontraron cursos de capacitación próximos.</p>
                        <a href="{{ route('capacitaciones.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Crear Nuevo Curso
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Funciones de toast
function showSuccessToast(message) {
    // Implementar toast de éxito
    console.log('Success:', message);
}

function showErrorToast(message) {
    // Implementar toast de error
    console.log('Error:', message);
}
</script>
@endsection
