@extends('partials.layouts.master')

@section('title', 'Ver Tipo de Elección | CLDCI')
@section('title-sub', 'Detalles del Tipo de Elección')
@section('pagetitle', 'Ver Tipo de Elección')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="ri-eye-line me-2"></i>
                    Detalles del Tipo de Elección
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('tipos-elecciones.edit', $tipoEleccion) }}" class="btn btn-soft-primary btn-sm">
                        <i class="ri-edit-line me-1"></i>
                        Editar
                    </a>
                    <a href="{{ route('tipos-elecciones.index') }}" class="btn btn-soft-secondary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Nombre del Tipo</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $tipoEleccion->color ?? 'secondary' }}-subtle text-{{ $tipoEleccion->color ?? 'secondary' }} me-2" style="font-size: 1.2rem; padding: 0.5rem;">
                                    <i class="{{ $tipoEleccion->icono ?? 'ri-question-line' }}"></i>
                                </span>
                                <span class="fs-5">{{ $tipoEleccion->nombre }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Estado</label>
                            <div>
                                @if($tipoEleccion->activo)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-checkbox-circle-line me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="ri-close-circle-line me-1"></i>Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Slug</label>
                            <p class="text-muted mb-0">
                                <code>{{ $tipoEleccion->slug }}</code>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Orden</label>
                            <p class="text-muted mb-0">{{ $tipoEleccion->orden }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Descripción</label>
                    <div class="bg-light p-3 rounded">
                        @if($tipoEleccion->descripcion)
                            {{ $tipoEleccion->descripcion }}
                        @else
                            <em class="text-muted">Sin descripción</em>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Icono</label>
                            <p class="text-muted mb-0">
                                <code>{{ $tipoEleccion->icono ?? 'ri-question-line' }}</code>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Color</label>
                            <p class="text-muted mb-0">
                                <code>{{ $tipoEleccion->color ?? 'secondary' }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Fecha de Creación</label>
                            <p class="text-muted mb-0">{{ $tipoEleccion->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Última Actualización</label>
                            <p class="text-muted mb-0">{{ $tipoEleccion->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($tipoEleccion->elecciones->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-calendar-event-line me-2"></i>
                    Elecciones Asociadas ({{ $tipoEleccion->elecciones->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Organización</th>
                                <th>Fecha Inicio</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tipoEleccion->elecciones->take(5) as $eleccion)
                            <tr>
                                <td>{{ $eleccion->titulo }}</td>
                                <td>{{ $eleccion->organizacion->nombre ?? 'N/A' }}</td>
                                <td>{{ $eleccion->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $eleccion->estado == 'activa' ? 'success' : ($eleccion->estado == 'programada' ? 'info' : 'secondary') }}-subtle text-{{ $eleccion->estado == 'activa' ? 'success' : ($eleccion->estado == 'programada' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($eleccion->estado) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('elecciones.show', $eleccion) }}" class="btn btn-soft-primary btn-sm">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($tipoEleccion->elecciones->count() > 5)
                    <div class="text-center mt-3">
                        <small class="text-muted">Mostrando 5 de {{ $tipoEleccion->elecciones->count() }} elecciones</small>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
