@extends('partials.layouts.master')

@section('title', 'Detalles del Miembro | CLDCI')
@section('title-sub', 'Información Completa')
@section('pagetitle', 'Detalles del Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
@endsection

@section('content')
<div class="row">
    <!-- Información del Miembro -->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($miembro->foto_url)
                    <img src="{{ $miembro->foto_url }}" alt="" class="avatar-lg rounded-circle">
                    @else
                    <div class="avatar-lg rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto">
                        <i class="ri-user-line fs-24"></i>
                    </div>
                    @endif
                </div>
                <h5 class="mb-1">{{ $miembro->nombre_completo }}</h5>
                <p class="text-muted mb-2">{{ $miembro->profesion }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $miembro->tipo_membresia === 'fundador' ? 'primary' : ($miembro->tipo_membresia === 'activo' ? 'success' : 'info') }} fs-12">
                        {{ ucfirst($miembro->tipo_membresia) }}
                    </span>
                    <span class="badge bg-{{ $miembro->estado_membresia === 'activa' ? 'success' : ($miembro->estado_membresia === 'suspendida' ? 'danger' : 'warning') }} fs-12">
                        {{ ucfirst($miembro->estado_membresia) }}
                    </span>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('miembros.carnet', $miembro->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="ri-qr-code-line me-1"></i> Ver Carnet
                    </a>
                    <a href="mailto:{{ $miembro->email }}" class="btn btn-outline-success btn-sm">
                        <i class="ri-mail-line me-1"></i> Contactar
                    </a>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de Contacto</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <p class="mb-0">{{ $miembro->email }}</p>
                </div>
                @if($miembro->telefono)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <p class="mb-0">{{ $miembro->telefono }}</p>
                </div>
                @endif
                @if($miembro->direccion)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dirección</label>
                    <p class="mb-0">{{ $miembro->direccion }}</p>
                </div>
                @endif
                @if($miembro->pais_residencia)
                <div class="mb-3">
                    <label class="form-label fw-semibold">País de Residencia</label>
                    <p class="mb-0">{{ $miembro->pais_residencia }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Información Institucional -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Institucional</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Número de Carnet</label>
                    <p class="mb-0 fw-semibold text-primary">{{ $miembro->numero_carnet }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Organización</label>
                    <p class="mb-0">{{ $miembro->organizacion->nombre }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha de Ingreso</label>
                    <p class="mb-0">{{ $miembro->fecha_ingreso->format('d/m/Y') }}</p>
                </div>
                @if($miembro->cedula)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cédula</label>
                    <p class="mb-0">{{ $miembro->cedula }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="col-xxl-8">
        <!-- Cargos Actuales -->
        @if($cargosActuales->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cargos Actuales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($cargosActuales as $cargo)
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-{{ $cargo->es_presidente ? 'primary' : 'success' }}-subtle text-{{ $cargo->es_presidente ? 'primary' : 'success' }} d-flex align-items-center justify-content-center">
                                        <i class="ri-{{ $cargo->es_presidente ? 'crown' : 'user' }}-line fs-16"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">{{ $cargo->cargo->nombre }}</h6>
                                    <p class="text-muted mb-0 fs-12">{{ $cargo->organo->nombre }}</p>
                                </div>
                            </div>
                            <p class="text-muted mb-0 fs-12">
                                <i class="ri-calendar-line me-1"></i>
                                Desde {{ $cargo->fecha_inicio->format('d/m/Y') }}
                                @if($cargo->fecha_fin)
                                hasta {{ $cargo->fecha_fin->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estadísticas del Miembro</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="ri-calendar-check-line fs-16"></i>
                            </div>
                            <h4 class="mb-1">{{ $estadisticas['asambleas_asistidas'] }}</h4>
                            <p class="text-muted mb-0 fs-12">Asambleas Asistidas</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="ri-graduation-cap-line fs-16"></i>
                            </div>
                            <h4 class="mb-1">{{ $estadisticas['cursos_completados'] }}</h4>
                            <p class="text-muted mb-0 fs-12">Cursos Completados</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="ri-vote-line fs-16"></i>
                            </div>
                            <h4 class="mb-1">{{ $estadisticas['votos_emitidos'] }}</h4>
                            <p class="text-muted mb-0 fs-12">Votos Emitidos</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="ri-user-settings-line fs-16"></i>
                            </div>
                            <h4 class="mb-1">{{ $estadisticas['cargos_actuales'] }}</h4>
                            <p class="text-muted mb-0 fs-12">Cargos Actuales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Asambleas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historial de Asambleas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Asamblea</th>
                                <th>Fecha</th>
                                <th>Asistencia</th>
                                <th>Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($miembro->asistenciaAsambleas as $asistencia)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $asistencia->asamblea->titulo }}</h6>
                                    <p class="text-muted mb-0 fs-12">{{ $asistencia->asamblea->organizacion->nombre }}</p>
                                </td>
                                <td>{{ $asistencia->asamblea->fecha_asamblea->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $asistencia->presente ? 'success' : 'danger' }}">
                                        {{ $asistencia->presente ? 'Asistió' : 'Ausente' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $asistencia->modalidad === 'presencial' ? 'primary' : 'success' }}">
                                        {{ ucfirst($asistencia->modalidad) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <div class="py-3">
                                        <i class="ri-calendar-line fs-24 text-muted mb-2 d-block"></i>
                                        <p class="mb-0">No hay historial de asambleas</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cursos Inscritos -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cursos Inscritos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($miembro->inscripcionesCursos as $inscripcion)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $inscripcion->curso->titulo }}</h6>
                                    <p class="text-muted mb-0 fs-12">{{ $inscripcion->curso->organizacion->nombre }}</p>
                                </td>
                                <td>{{ $inscripcion->curso->fecha_inicio->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $inscripcion->estado === 'completo' ? 'success' : ($inscripcion->estado === 'asistio' ? 'info' : 'warning') }}">
                                        {{ ucfirst($inscripcion->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $inscripcion->curso->modalidad === 'presencial' ? 'primary' : ($inscripcion->curso->modalidad === 'virtual' ? 'success' : 'info') }}">
                                        {{ ucfirst($inscripcion->curso->modalidad) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <div class="py-3">
                                        <i class="ri-book-open-line fs-24 text-muted mb-2 d-block"></i>
                                        <p class="mb-0">No hay cursos inscritos</p>
                                    </div>
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
@endsection
