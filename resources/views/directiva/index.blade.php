@extends('partials.layouts.master')

@section('title', 'Directiva')
@section('title-sub', 'Directiva')
@section('pagetitle', 'Estructura Directiva')

@section('content')
<div class="row">
    <!-- Estadísticas de Directiva -->
    <div class="col-xl-3 col-md-6">
        <x-miembros.stat-card 
            title="Períodos Totales" 
            value="{{ $estadisticas['total_periodos'] }}" 
            icon="ri-calendar-line" 
            background="bg-primary-subtle" 
            iconBackground="bg-primary" 
        />
    </div>
    <div class="col-xl-3 col-md-6">
        <x-miembros.stat-card 
            title="Períodos Activos" 
            value="{{ $estadisticas['periodos_activos'] }}" 
            icon="ri-checkbox-circle-line" 
            background="bg-success-subtle" 
            iconBackground="bg-success" 
        />
    </div>
    <div class="col-xl-3 col-md-6">
        <x-miembros.stat-card 
            title="Miembros Directiva" 
            value="{{ $estadisticas['miembros_directiva_actual'] }}" 
            icon="ri-user-star-line" 
            background="bg-info-subtle" 
            iconBackground="bg-info" 
        />
    </div>
    <div class="col-xl-3 col-md-6">
        <x-miembros.stat-card 
            title="Organizaciones Activas" 
            value="{{ $estadisticas['organizaciones_activas'] }}" 
            icon="ri-building-line" 
            background="bg-warning-subtle" 
            iconBackground="bg-warning" 
        />
    </div>
</div>

<div class="row">
    <!-- Directiva Actual -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-shrink-0">
                        <h4 class="card-title mb-0">
                            <i class="ri-user-star-line me-2"></i>
                            Directiva Actual
                        </h4>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('directiva.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i>
                            Nuevo Período
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($directivaActual)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Período</h6>
                            <p class="mb-3">
                                <i class="ri-calendar-line me-1"></i>
                                {{ \Carbon\Carbon::parse($directivaActual->fecha_inicio)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($directivaActual->fecha_fin)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Estado</h6>
                            <span class="badge bg-success">Activo</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Cargo</th>
                                    <th>Miembro</th>
                                    <th>Organización</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($miembrosDirectiva as $miembro)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $miembro->cargo_directiva }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if($miembro->foto_url)
                                                    <img src="{{ asset('storage/' . $miembro->foto_url) }}" 
                                                         class="rounded-circle" 
                                                         alt="{{ $miembro->nombre_completo }}">
                                                @else
                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                        {{ substr($miembro->nombre_completo, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $miembro->nombre_completo }}</h6>
                                                <small class="text-muted">{{ $miembro->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $miembro->organizacion->nombre }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $miembro->estadoMembresia->nombre }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="ri-user-line fs-1 mb-3 d-block"></i>
                                        No hay miembros en la directiva actual
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-user-star-line fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay directiva activa</h5>
                        <p class="text-muted">Crea un nuevo período de directiva para comenzar.</p>
                        <a href="{{ route('directiva.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i>
                            Crear Directiva
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historial de Períodos -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="ri-history-line me-2"></i>
                    Historial de Períodos
                </h4>
            </div>
            <div class="card-body">
                @forelse($periodosActivos as $periodo)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-calendar-line"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">
                            {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('Y') }} - 
                            {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('Y') }}
                        </h6>
                        <p class="text-muted mb-0 fs-13">
                            {{ \Carbon\Carbon::parse($periodo->fecha_inicio)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($periodo->fecha_fin)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('directiva.show', $periodo->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ri-eye-line"></i>
                        </a>
                    </div>
                </div>
                @if(!$loop->last)
                    <hr class="my-3">
                @endif
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ri-history-line fs-1 mb-3"></i>
                    <p class="mb-0">No hay períodos registrados</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection