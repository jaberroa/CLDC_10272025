@extends('partials.layouts.master')

@section('title', 'Editar Asamblea | CLDCI')
@section('title-sub', 'Gestión de Asambleas')
@section('pagetitle', 'Editar Asamblea')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/asambleas-create-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header asambleas-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('asambleas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-edit-line"></i>
                            Editar Asamblea
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Modifique la información de la asamblea en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-asambleas.form
                    :asamblea="$asamblea"
                    :organizaciones="$organizaciones"
                    submit-label="Actualizar Asamblea"
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
@include('asambleas.partials.form-scripts')
@endsection