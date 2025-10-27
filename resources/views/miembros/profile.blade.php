@extends('partials.layouts.master')

@section('title', 'Perfil de Miembro | CLDCI')
@section('title-sub', 'Miembros')
@section('pagetitle', 'Perfil de Miembro')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('assets/libs/qrcode/qrcode.min.css') }}"> -->
<link rel="stylesheet" href="{{ asset('css/miembros/profile.css') }}">
@endsection

@section('content')
<div class="pages-profile profile-layout">       
</div>

<div class="main-profile-bg position-relative">
    <div class="profile-bg">
        <img src="{{ asset('assets/images/p-bg.jpg') }}" alt="Profile Background" class="w-100 h-100 object-fit-cover">
    </div>
</div>

<!-- Estructura Original de Urbix -->
<div class="pages-profile">       
</div>

<div class="main-profile-bg position-relative">
    <div class="profile-bg">
        <img src="{{ asset('assets/images/p-bg.jpg') }}" alt="Profile Background" class="w-100 h-100 object-fit-cover">
    </div>
</div>
<div class="position-relative z-1 text-end edit-btn">
    <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn border border-white text-white">Editar Perfil</a>
</div>
<div class="card overflow-hidden position-relative z-1">
    <div class="card-body p-5">
        <div class="d-flex justify-content-between flex-wrap align-items-center gap-6">
            <div class="flex-shrink-0">
                <div class="position-relative d-inline-block">
                    <img src="{{ $miembro->foto ? asset('storage/' . $miembro->foto) : asset('assets/images/avatar/avatar-3.jpg') }}" alt="Avatar Image" class="h-100px w-100px rounded-pill">
                    <div class="h-30px w-30px rounded-pill bg-primary d-flex justify-content-center align-items-center text-white border border-3 border-light-subtle position-absolute fs-12 bottom-0 end-0">
                        <i class="bi bi-camera"></i>
                    </div>
                    <span class="position-absolute profile-dot bg-success rounded-circle">
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1">{{ $miembro->nombre }} {{ $miembro->apellido }} <i class="bi bi-patch-check-fill fs-16 ms-1 text-success"></i>
                </h4>
                <p class="text-muted mb-1">{{ ucfirst($miembro->tipo_membresia) }}</p>
                <p class="text-muted mb-0">{{ $miembro->organizacion->nombre }}</p>
            </div>
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['asambleas_asistidas'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Asambleas</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['capacitaciones_inscrito'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Capacitaciones</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['elecciones_participado'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Elecciones</span>
                </div>
            </div>
            <div class="d-flex float-end gap-2 flex-shrink-0">
                <a href="{{ route('miembros.carnet', $miembro->id) }}" class="btn btn-light">Ver Carnet</a>
                <button type="button" class="btn btn-primary" onclick="imprimirPerfil()"><i class="bi bi-printer me-1"></i>Imprimir</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3">
        <x-miembros.profile-personal-details :miembro="$miembro" />
        <x-miembros.profile-skills :miembro="$miembro" :estadisticas="$estadisticas" />
        <x-miembros.profile-social :miembro="$miembro" />
    </div>
    <div class="col-xl-6 order-last order-xl-2">
        <div class="tab-content">
            <div class="tab-pane active show" id="pages-profile-tab" role="tabpanel">
                <x-miembros.profile-about :miembro="$miembro" />
                <x-miembros.profile-activity :actividadReciente="$actividadReciente ?? []" />
            </div>
            <div class="tab-pane" id="pages-projects-tab" role="tabpanel">
                <x-miembros.profile-projects :miembro="$miembro" :estadisticas="$estadisticas" />
            </div>
            <div class="tab-pane" id="pages-post-tab" role="tabpanel">
                <x-miembros.profile-posts :miembro="$miembro" />
            </div>
            <div class="tab-pane" id="pages-team-tab" role="tabpanel">
                <x-miembros.profile-teams :miembro="$miembro" :cargosActuales="$cargosActuales ?? []" />
            </div>
            <div class="tab-pane" id="pages-documentation-tab" role="tabpanel">
                <x-miembros.profile-documentation :miembro="$miembro" />
            </div>
            <div class="tab-pane" id="pages-settings-tab" role="tabpanel">
                <x-miembros.profile-settings :miembro="$miembro" />
            </div>
        </div>
    </div>
    <div class="col-xl-3 order-2 order-xl-last">
        <div class="d-flex flex-column">
            <div class="card order-2 order-xl-1">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pages-profile-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-home-2-line me-3"></i>Perfil</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-projects-tab" role="tab" aria-selected="true">
                                <div class="fw-semibold"><i class="ri-user-3-line me-3"></i>Proyectos</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-post-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-mail-line me-3"></i>Publicaciones</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-team-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-team-line me-3"></i>Equipos</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-documentation-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-file-text-line me-3"></i>Documentación</div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-settings-tab" role="tab" aria-selected="false" tabindex="-1">
                                <div class="fw-semibold"><i class="ri-settings-3-line me-3"></i>Configuración</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card order-1 order-xl-2">
                <div class="card-header">
                    <h5 class="card-title mb-0">Conexiones</h5>
                </div>
                <div class="card-body d-flex flex-column gap-6">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" class="rounded-circle avatar-md" alt="User">
                        <div class="text-truncate">
                            <h6 class="mb-0">Miembro CLDCI</h6>
                            <small class="text-muted">Miembro Activo | {{ $miembro->fecha_ingreso->format('M d, Y') }}</small>
                        </div>
                        <button class="btn btn-light-primary icon-btn ms-auto"><i class="ri-user-add-line"></i></button>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/images/avatar/avatar-4.jpg') }}" class="rounded-circle avatar-md" alt="User">
                        <div class="text-truncate">
                            <h6 class="mb-0">Directiva CLDCI</h6>
                            <small class="text-muted">Directivo | {{ $miembro->fecha_ingreso->format('M d, Y') }}</small>
                        </div>
                        <button class="btn btn-light-primary icon-btn ms-auto"><i class="ri-user-add-line"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sección de tabs eliminada - ahora se maneja con navegación lateral --}}

<!-- Modal del Carnet Digital -->
@include('components.miembros.carnet-modal')
@endsection

@section('js')
<!-- QR Code js -->
<!-- <script src="{{ asset('assets/libs/qrcode/qrcode.min.js') }}"></script> -->
<!-- html2canvas js -->
<!-- <script src="{{ asset('assets/libs/html2canvas/html2canvas.min.js') }}"></script> -->
<!-- jsPDF js -->
<!-- <script src="{{ asset('assets/libs/jspdf/jspdf.min.js') }}"></script> -->
<!-- Dropzone js -->
<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
<!-- Profile js -->
<script src="{{ asset('js/miembros/profile.js') }}"></script>
@endsection
