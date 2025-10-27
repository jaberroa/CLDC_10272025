@extends('partials.layouts.master')

@section('title', 'Crear Capacitación | CLDCI')
@section('title-sub', 'Gestión de Capacitaciones')
@section('pagetitle', 'Crear Nueva Capacitación')

@section('css')
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
                        <a href="{{ route('capacitaciones.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-graduation-cap-line"></i>
                            Crear Nueva Capacitación
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información del curso de capacitación para registrarlo en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-capacitaciones.form
                    submitLabel="Guardar Capacitación"
                    submitIcon="ri-save-line"
                    submitClass="btn btn-primary"
                    cancelLabel="Cancelar"
                    cancelIcon="ri-close-line"
                    cancelClass="btn btn-outline-secondary"
                    alignButtons="start"
                />
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@include('capacitaciones.partials.form-scripts')
@endsection
