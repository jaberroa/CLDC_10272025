@extends('partials.layouts.master')

@section('title', 'Editar Miembro | CLDCI')
@section('title-sub', 'Gestión de Miembros')
@section('pagetitle', 'Editar Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-edit-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-edit-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('miembros.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-user-edit-line"></i>
                            Editar Miembro: {{ $miembro->nombre_completo }}
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Modifique la información del miembro según sea necesario
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-miembros.form
                    :miembro="$miembro"
                    :organizaciones="$organizaciones"
                    :estados-membresia="$estadosMembresia"
                    submit-label="Guardar Cambios"
                    submit-icon="ri-save-line"
                    submit-class="btn btn-primary"
                    cancel-label="Cancelar"
                    cancel-icon="ri-close-line"
                    cancel-class="btn btn-secondary"
                    align-buttons="end"
                >
                    <x-slot name="extra">
                        <!-- Información del Carnet -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ri-qr-code-line text-primary fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">Información del Carnet</h5>
                                                <p class="text-muted mb-0">Datos del carnet digital del miembro</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Número de Carnet</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary-subtle border-primary-subtle">
                                                        <i class="ri-id-card-line text-primary"></i>
                                                    </span>
                                                    <input type="text" class="form-control border-primary-subtle bg-light" 
                                                           value="{{ $miembro->numero_carnet }}" readonly>
                                                </div>
                                                <small class="text-muted">Este número se genera automáticamente y no se puede modificar.</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Estado del Carnet</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success-subtle text-success fs-6 me-2">
                                                        <i class="ri-check-line me-1"></i>
                                                        Activo
                                                    </span>
                                                    <small class="text-muted">Carnet válido y en uso</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                </x-miembros.form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Dropzone js -->
<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
<!-- Select2 js -->
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<!-- Form Validation js -->
<script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
<!-- File Upload js -->
<script src="{{ asset('assets/js/form/file-upload.init.js') }}"></script>
@include('miembros.partials.form-scripts')
@endsection
