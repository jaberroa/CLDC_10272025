@extends('partials.layouts.master')

@section('title', 'Editar Organización | CLDCI')
@section('title-sub', 'Gestión de Organizaciones')
@section('pagetitle', 'Editar Organización')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/organizaciones-edit-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/organizaciones/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header organizaciones-edit-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('organizaciones.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-building-edit-line"></i>
                            Editar Organización: {{ $organizacion->nombre }}
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Modifique la información de la organización según sea necesario
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-organizaciones.form
                    :organizacion="$organizacion"
                    :tipos-organizacion="$tiposOrganizacion"
                    submit-label="Guardar Cambios"
                    submit-icon="ri-save-line"
                    submit-class="btn btn-primary"
                    cancel-label="Cancelar"
                    cancel-icon="ri-close-line"
                    cancel-class="btn btn-secondary"
                    align-buttons="end"
                >
                    <x-slot name="extra">
                        <!-- Información adicional para edición -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="ri-information-line"></i>
                                            Información de la Organización
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Código Actual</label>
                                                <input type="text" class="form-control" value="{{ $organizacion->codigo }}" readonly>
                                                <small class="text-muted">El código no se puede modificar</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Fecha de Creación</label>
                                                <input type="text" class="form-control" value="{{ $organizacion->created_at->format('d/m/Y H:i:s') }}" readonly>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Última Actualización</label>
                                                <input type="text" class="form-control" value="{{ $organizacion->updated_at->format('d/m/Y H:i:s') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="ri-bar-chart-line"></i>
                                            Estadísticas
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h4 class="text-primary mb-0">{{ $organizacion->miembros()->count() }}</h4>
                                                    <small class="text-muted">Miembros</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h4 class="text-success mb-0">{{ $organizacion->miembros()->activos()->count() }}</h4>
                                                    <small class="text-muted">Activos</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h4 class="text-info mb-0">{{ $organizacion->asambleas()->count() }}</h4>
                                                    <small class="text-muted">Asambleas</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h4 class="text-warning mb-0">{{ $organizacion->elecciones()->count() }}</h4>
                                                    <small class="text-muted">Elecciones</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                </x-organizaciones.form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
<script src="{{ vite_asset('resources/js/organizaciones/app.js') }}"></script>
@endsection

