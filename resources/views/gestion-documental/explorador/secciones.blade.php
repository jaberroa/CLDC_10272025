@extends('partials.layouts.master')

@section('title', 'Mi Unidad | CLDCI')
@section('title-sub', 'Gestión de Documentos')
@section('pagetitle', 'Mi Unidad')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<style>
    .seccion-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 2rem;
        transition: all 0.3s;
        cursor: pointer;
        height: 100%;
    }
    
    .seccion-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }
    
    .seccion-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 1rem;
    }
</style>
@endsection

@section('content')
<!-- Estadísticas -->
<div class="row mb-3">
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Total Carpetas"
            :value="number_format($estadisticas['total_carpetas'] ?? 0)"
            icon="ri-folder-line"
            background="bg-primary-subtle"
            icon-background="bg-primary"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Total Archivos"
            :value="number_format($estadisticas['total_archivos'] ?? 0)"
            icon="ri-file-text-line"
            background="bg-success-subtle"
            icon-background="bg-success"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Espacio Usado"
            :value="number_format(($estadisticas['espacio_usado'] ?? 0) / 1024 / 1024, 2) . ' MB'"
            icon="ri-database-line"
            background="bg-info-subtle"
            icon-background="bg-info"
        />
    </div>
    <div class="col-xxl col-sm-6 col-md-3">
        <x-miembros.stat-card
            title="Compartidos"
            :value="number_format($estadisticas['total_compartidos'] ?? 0)"
            icon="ri-share-line"
            background="bg-warning-subtle"
            icon-background="bg-warning"
        />
    </div>
</div>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">Selecciona una Sección</h4>
                <p class="text-muted mb-0">Explora tus documentos por sección organizativa</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @foreach($secciones as $seccion)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="seccion-card" onclick="window.location='{{ route('gestion-documental.explorador.index') }}?seccion_id={{ $seccion->id }}'">
                <div class="seccion-icon" style="background: {{ $seccion->color }}15; color: {{ $seccion->color ?? '#0d6efd' }};">
                    <i class="{{ $seccion->icono ?? 'ri-folder-line' }}"></i>
                </div>
                <h5 class="text-center mb-2">{{ $seccion->nombre }}</h5>
                <p class="text-center text-muted small mb-3">{{ $seccion->descripcion }}</p>
                <div class="d-flex justify-content-around text-center">
                    <div>
                        <div class="fw-bold">{{ $seccion->carpetas()->count() }}</div>
                        <small class="text-muted">Carpetas</small>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $seccion->documentos()->count() }}</div>
                        <small class="text-muted">Archivos</small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

