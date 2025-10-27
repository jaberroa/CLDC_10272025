@extends('partials.layouts.master')

@section('title', 'Perfil de Miembro | CLDCI')
@section('title-sub', 'Miembros')
@section('pagetitle', 'Perfil de Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/qrcode/qrcode.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/miembros/profile.css') }}">
@endsection

@section('content')
<div class="pages-profile">       
</div>

<div class="main-profile-bg position-relative">
    <div class="profile-bg">
        <img src="{{ asset('assets/images/p-bg.jpg') }}" alt="Profile Background" class="w-100 h-100 object-fit-cover">
    </div>
</div>

<!-- Header del Perfil -->
<x-miembros.profile-header :miembro="$miembro" :estadisticas="$estadisticas" />

<div class="row">
    <!-- Estadísticas del Perfil -->
    <x-miembros.profile-stats :miembro="$miembro" :estadisticas="$estadisticas" />
    
    <!-- Cargos Actuales -->
    <x-miembros.profile-cargos :miembro="$miembro" :cargosActuales="$cargosActuales ?? []" />
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pages-overview-tab" role="tab" aria-selected="true">
                            <i class="ri-user-line me-1"></i> Resumen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pages-projects-tab" role="tab" aria-selected="false">
                            <i class="ri-calendar-line me-1"></i> Actividades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pages-details-tab" role="tab" aria-selected="false">
                            <i class="ri-file-list-line me-1"></i> Historial
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="pages-overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Resumen del Miembro</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-3">Información General</h6>
                                                <p class="mb-2">
                                                    <strong>Nombre:</strong> {{ $miembro->nombre }} {{ $miembro->apellido }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Email:</strong> {{ $miembro->email }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Teléfono:</strong> {{ $miembro->telefono ?? 'No especificado' }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Organización:</strong> {{ $miembro->organizacion->nombre }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-3">Membresía</h6>
                                                <p class="mb-2">
                                                    <strong>Tipo:</strong> {{ ucfirst($miembro->tipo_membresia) }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Estado:</strong> {{ ucfirst($miembro->estado_membresia) }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Número de Carnet:</strong> {{ $miembro->numero_carnet }}
                                                </p>
                                                <p class="mb-2">
                                                    <strong>Fecha de Ingreso:</strong> {{ $miembro->fecha_ingreso->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <h6 class="fw-semibold mb-3">Descripción</h6>
                                            <p class="mb-0">
                                                Como {{ $miembro->tipo_membresia }}, {{ $miembro->nombre }} ha contribuido significativamente al desarrollo de la organización a través de su participación en asambleas, capacitaciones y procesos electorales. Su dedicación y profesionalismo son un ejemplo para otros miembros.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="pages-projects-tab" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Participación en Actividades</h5>
                                <div class="mb-5 pb-5 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Asambleas Generales</h6>
                                        <span class="badge bg-primary">Participante Activo</span>
                                    </div>
                                    <p class="text-muted mb-3">Total Asistidas: <span class="text-body">{{ $estadisticas['asambleas_asistidas'] }}</span></p>
                                    <p>El miembro ha demostrado un compromiso constante con la participación democrática en las asambleas generales de la organización.</p>
                                </div>
                                <div class="mb-5 pb-5 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Capacitaciones</h6>
                                        <span class="badge bg-success">En Progreso</span>
                                    </div>
                                    <p class="text-muted mb-3">Total Inscrito: <span class="text-body">{{ $estadisticas['capacitaciones_inscrito'] }}</span></p>
                                    <p>Participación activa en programas de formación y desarrollo profesional ofrecidos por la organización.</p>
                                </div>
                                <div class="mb-5 pb-5 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Procesos Electorales</h6>
                                        <span class="badge bg-info">Participante</span>
                                    </div>
                                    <p class="text-muted mb-3">Total Participado: <span class="text-body">{{ $estadisticas['elecciones_participado'] }}</span></p>
                                    <p>Ejercicio responsable del derecho al voto en los procesos democráticos de la organización.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="pages-details-tab" role="tabpanel">
                        <!-- Historial del Perfil -->
                        <x-miembros.profile-historial 
                            :miembro="$miembro" 
                            :actividadReciente="$actividadReciente ?? []" 
                            :asambleasHistorial="$asambleasHistorial ?? []" 
                            :capacitacionesHistorial="$capacitacionesHistorial ?? []" 
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<x-miembros.profile-actions :miembro="$miembro" />

<!-- Modal del Carnet Digital -->
@include('components.miembros.carnet-modal')
@endsection

@section('js')
<!-- QR Code js -->
<script src="{{ asset('assets/libs/qrcode/qrcode.min.js') }}"></script>
<!-- html2canvas js -->
<script src="{{ asset('assets/libs/html2canvas/html2canvas.min.js') }}"></script>
<!-- jsPDF js -->
<script src="{{ asset('assets/libs/jspdf/jspdf.min.js') }}"></script>
<!-- Profile js -->
<script src="{{ asset('js/miembros/profile.js') }}"></script>
@endsection


