@extends('partials.layouts.master')

@section('title', 'Agregar Miembro | CLDCI')
@section('title-sub', 'Gestión de Miembros')
@section('pagetitle', 'Agregar Nuevo Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-create-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('miembros.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-user-add-line"></i>
                            Agregar Nuevo Miembro
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información del miembro para registrarlo en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-miembros.form
                    :organizaciones="$organizaciones"
                    :estados-membresia="$estadosMembresia"
                    submit-label="Guardar Miembro"
                    submit-icon="ri-save-line"
                    submit-class="btn btn-primary"
                    cancel-label="Cancelar"
                    cancel-icon="ri-close-line"
                    cancel-class="btn btn-outline-secondary"
                    align-buttons="start"
                />
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
@include('miembros.partials.form-scripts')
@endsection
