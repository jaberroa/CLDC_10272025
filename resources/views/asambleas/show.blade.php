@extends('partials.layouts.master')

@section('title', 'Detalles de Asamblea | CLDCI')
@section('title-sub', 'Gestión de Asambleas')
@section('pagetitle', 'Detalles de Asamblea')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/gridjs.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-table-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-selection.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('asambleas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-calendar-line"></i>
                            Detalles de la Asamblea
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Información completa de la asamblea: {{ $asamblea->titulo }}
                        </p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('asambleas.asistencias.index', ['asamblea_id' => $asamblea->id]) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-user-line me-1"></i> Asistencias
                        </a>
                        <a href="{{ route('asambleas.edit', $asamblea) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-edit-line me-1"></i> Editar
                        </a>
                        @if($asamblea->estado === 'convocada')
                            <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="en_proceso">
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="ri-play-line me-1"></i> Iniciar
                                </button>
                            </form>
                        @elseif($asamblea->estado === 'en_proceso')
                            <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="finalizada">
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="ri-check-line me-1"></i> Completar
                                </button>
                            </form>
                        @endif
                        @if($asamblea->estado !== 'finalizada' && $asamblea->estado !== 'cancelada')
                            <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="cancelada">
                                <button type="submit" class="btn btn-outline-light btn-sm" onclick="return confirm('¿Está seguro de cancelar esta asamblea?')">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Información Básica -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="ri-information-line me-2"></i>
                            Información Básica
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Título</label>
                            <p class="form-control-plaintext">{{ $asamblea->titulo }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $asamblea->tipo_asamblea_color }}-subtle text-{{ $asamblea->tipo_asamblea_color }}">
                                    {{ ucfirst($asamblea->tipo) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripción</label>
                            <p class="form-control-plaintext">{{ $asamblea->descripcion ?: 'Sin descripción' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Fechas y Horarios -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="ri-calendar-line me-2"></i>
                            Fechas y Horarios
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha de Convocatoria</label>
                            <p class="form-control-plaintext">{{ $asamblea->fecha_convocatoria->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha de la Asamblea</label>
                            <p class="form-control-plaintext">{{ $asamblea->fecha_asamblea->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="ri-map-pin-line me-2"></i>
                            Ubicación
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lugar</label>
                            <p class="form-control-plaintext">{{ $asamblea->lugar }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modalidad</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info-subtle text-info">
                                    {{ ucfirst($asamblea->modalidad) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($asamblea->enlace_virtual)
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enlace Virtual</label>
                            <p class="form-control-plaintext">
                                <a href="{{ $asamblea->enlace_virtual }}" target="_blank" class="text-primary">
                                    {{ $asamblea->enlace_virtual }}
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quorum y Asistencia -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="ri-group-line me-2"></i>
                            Quorum y Asistencia
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quorum Mínimo</label>
                            <p class="form-control-plaintext">{{ $asamblea->quorum_minimo }} miembros</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asistentes Confirmados</label>
                            <p class="form-control-plaintext">{{ $asamblea->asistentes_count }} miembros</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quorum Alcanzado</label>
                            <p class="form-control-plaintext">
                                @if($asamblea->quorum_alcanzado)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-line me-1"></i>Sí
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="ri-time-line me-1"></i>No
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">
                            <i class="ri-information-line me-2"></i>
                            Estado
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Estado Actual</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $asamblea->estado_color }}-subtle text-{{ $asamblea->estado_color }}">
                                    {{ ucfirst($asamblea->estado) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Días Restantes</label>
                            <p class="form-control-plaintext">
                                @if($asamblea->dias_restantes > 0)
                                    {{ $asamblea->dias_restantes }} días
                                @elseif($asamblea->dias_restantes == 0)
                                    Hoy
                                @else
                                    {{ abs($asamblea->dias_restantes) }} días atrás
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            @if($asamblea->estado === 'convocada')
                                <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="en_proceso">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-play-line me-1"></i>
                                        Iniciar Asamblea
                                    </button>
                                </form>
                            @elseif($asamblea->estado === 'en_proceso')
                                <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="finalizada">
                                    <button type="submit" class="btn btn-info">
                                        <i class="ri-check-line me-1"></i>
                                        Completar Asamblea
                                    </button>
                                </form>
                            @endif
                            
                            @if($asamblea->estado !== 'finalizada' && $asamblea->estado !== 'cancelada')
                                <form action="{{ route('asambleas.update', $asamblea) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="cancelada">
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('¿Está seguro de cancelar esta asamblea?')">
                                        <i class="ri-close-line me-1"></i>
                                        Cancelar Asamblea
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
