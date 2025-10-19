@extends('partials.layouts.master')

@section('title', 'Dashboard | CLDCI - Sistema de Gestión')
@section('title-sub', 'Panel Principal')
@section('pagetitle', 'CLDCI')
@section('css')
 <link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
    <!-- Picker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
@endsection
@section('content')


                <div class="row">
                    <div class="col-xxl-8">
                        <div class="row">
                                <div class="col-xxl col-sm-6">
                                    <div class="card overflow-hidden">
                                        <div class="card-body bg-info-subtle position-relative z-1">
                                            <div class="d-flex gap-2">
                                                <div class="school-icon bg-info d-flex justify-content-center align-items-center fs-4">
                                                    <i class="ri-user-line" id="hexagon"></i>
                                                </div>
                                                <div class="text-center">
                                                    <span class="d-block fw-semibold mb-2 fs-5">Miembros Activos</span>
                                                    <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['miembros_activos']) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-xxl col-sm-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body bg-primary-subtle position-relative z-1">
                                        <div class="d-flex gap-2">
                                            <div class="school-icon bg-primary d-flex justify-content-center align-items-center fs-4">
                                                <i class="ri-building-line" id="hexagon"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="d-block fw-semibold mb-2 fs-5">Organizaciones</span>
                                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['organizaciones_activas']) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl col-sm-6">
                                <div class="card overflow-hidden">
                                    <div class="card-body bg-danger-subtle position-relative z-1">
                                        <div class="d-flex gap-2">
                                            <div class="school-icon bg-danger d-flex justify-content-center align-items-center fs-4">
                                                <i class="ri-calendar-line" id="hexagon"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="d-block fw-semibold mb-2 fs-5">Asambleas</span>
                                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['asambleas_programadas']) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card card-h-100">
                                    <div class="card-header">
                                        <h4>Transacciones Recientes</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="expenses-table table-box table-responsive" data-simplebar>
                                            <table class="table table-hover text-nowrap">
                                                    <thead class="table-light border-0">
                                                        <tr>
                                                            <th class="text-muted">ID</th>
                                                            <th class="text-muted">Tipo</th>
                                                            <th class="text-muted">Concepto</th>
                                                            <th class="text-muted">Monto</th>
                                                            <th class="text-muted">Categoría</th>
                                                            <th class="text-muted">Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($transaccionesRecientes as $transaccion)
                                                        <tr>
                                                            <td class="fw-semibold">#{{ substr($transaccion->id, 0, 8) }}</td>
                                                            <td class="fw-semibold">
                                                                <span class="badge {{ $transaccion->tipo == 'ingreso' ? 'bg-success' : 'bg-danger' }} px-3 rounded-3">
                                                                    {{ ucfirst($transaccion->tipo) }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-semibold">{{ $transaccion->concepto }}</td>
                                                            <td class="fw-semibold">${{ number_format($transaccion->monto, 2) }}</td>
                                                            <td class="fw-semibold">{{ ucfirst($transaccion->categoria) }}</td>
                                                            <td class="fw-semibold">{{ $transaccion->fecha->format('d/m/Y') }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">No hay transacciones recientes</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card card-h-100">
                                    <div class="card-header">
                                        <h4>Miembros Más Activos</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-tabs-bordered mb-4" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Asambleas</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Capacitaciones</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Elecciones</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade performer-table show active" data-simplebar id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                                <div class="table-box table-responsive">
                                                    <table class="table table-hover text-nowrap">
                                                        <thead class="table-light border-0">
                                                            <tr>
                                                                <th class="text-muted">Miembro</th>
                                                                <th class="text-muted">Seccional</th>
                                                                <th class="text-muted">Asistencias</th>
                                                                <th class="text-muted">Rendimiento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($topPerformersAsambleas as $index => $miembro)
                                                            <tr>
                                                                <td class="fw-semibold">
                                                                    <img src="{{ asset($miembro['foto']) }}" class="avatar-md rounded-circle" alt="Avatar Image">
                                                                    <span class="ms-3">{{ $miembro['nombre_solo'] }}</span>
                                                                </td>
                                                                <td class="fw-semibold">{{ $miembro['seccional'] }}</td>
                                                                <td class="fw-semibold">{{ $miembro['asistencias'] }} asambleas</td>
                                                                <td>
                                                                    <div class="w-100">
                                                                        <div class="d-flex justify-content-between align-items-center fs-13">
                                                                            <p class="text-{{ $miembro['porcentaje'] >= 90 ? 'success' : ($miembro['porcentaje'] >= 75 ? 'info' : 'primary') }} mb-1">{{ $miembro['porcentaje'] }}%</p>
                                                                            <span class="badge bg-{{ $miembro['badge_color'] }}">{{ $miembro['nivel'] }}</span>
                                                                        </div>
                                                                        <div class="progress progress-sm">
                                                                            <div class="progress-bar bg-{{ $miembro['porcentaje'] >= 90 ? 'success' : ($miembro['porcentaje'] >= 75 ? 'info' : 'primary') }}" style="width: {{ $miembro['porcentaje'] }}%"></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-4">
                                                                    <i class="ri-user-line fs-1 mb-3"></i>
                                                                    <p class="mb-0">No hay datos de asistencia disponibles</p>
                                                                </td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade performer-table" data-simplebar id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                                <div class="table-box table-responsive">
                                                    <table class="table table-hover text-nowrap">
                                                        <thead class="table-light border-0">
                                                            <tr>
                                                                <th class="text-muted">Miembro</th>
                                                                <th class="text-muted">Seccional</th>
                                                                <th class="text-muted">Cursos</th>
                                                                <th class="text-muted">Rendimiento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($topPerformersCapacitaciones as $index => $miembro)
                                                            <tr>
                                                                <td class="fw-semibold">
                                                                    <img src="{{ asset($miembro['foto']) }}" class="avatar-md rounded-circle" alt="Avatar Image">
                                                                    <span class="ms-3">{{ $miembro['nombre_solo'] }}</span>
                                                                </td>
                                                                <td class="fw-semibold">{{ $miembro['seccional'] }}</td>
                                                                <td class="fw-semibold">{{ $miembro['asistencias'] }} cursos</td>
                                                                <td>
                                                                    <div class="w-100">
                                                                        <div class="d-flex justify-content-between align-items-center fs-13">
                                                                            <p class="text-{{ $miembro['porcentaje'] >= 80 ? 'success' : ($miembro['porcentaje'] >= 60 ? 'info' : 'primary') }} mb-1">{{ $miembro['porcentaje'] }}%</p>
                                                                            <span class="badge bg-{{ $miembro['badge_color'] }}">{{ $miembro['nivel'] }}</span>
                                                                        </div>
                                                                        <div class="progress progress-sm">
                                                                            <div class="progress-bar bg-{{ $miembro['porcentaje'] >= 80 ? 'success' : ($miembro['porcentaje'] >= 60 ? 'info' : 'primary') }}" style="width: {{ $miembro['porcentaje'] }}%"></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-4">
                                                                    <i class="ri-graduation-cap-line fs-1 mb-3"></i>
                                                                    <p class="mb-0">No hay datos de capacitaciones disponibles</p>
                                                                </td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade performer-table" data-simplebar id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                                                <div class="table-box table-responsive">
                                                    <table class="table table-hover text-nowrap">
                                                        <thead class="table-light border-0">
                                                            <tr>
                                                                <th class="text-muted">Miembro</th>
                                                                <th class="text-muted">Seccional</th>
                                                                <th class="text-muted">Elecciones</th>
                                                                <th class="text-muted">Rendimiento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($topPerformersElecciones as $index => $miembro)
                                                            <tr>
                                                                <td class="fw-semibold">
                                                                    <img src="{{ asset($miembro['foto']) }}" class="avatar-md rounded-circle" alt="Avatar Image">
                                                                    <span class="ms-3">{{ $miembro['nombre_solo'] }}</span>
                                                                </td>
                                                                <td class="fw-semibold">{{ $miembro['seccional'] }}</td>
                                                                <td class="fw-semibold">{{ $miembro['asistencias'] }} elecciones</td>
                                                                <td>
                                                                    <div class="w-100">
                                                                        <div class="d-flex justify-content-between align-items-center fs-13">
                                                                            <p class="text-{{ $miembro['porcentaje'] >= 100 ? 'success' : ($miembro['porcentaje'] >= 75 ? 'info' : 'primary') }} mb-1">{{ $miembro['porcentaje'] }}%</p>
                                                                            <span class="badge bg-{{ $miembro['badge_color'] }}">{{ $miembro['nivel'] }}</span>
                                                                        </div>
                                                                        <div class="progress progress-sm">
                                                                            <div class="progress-bar bg-{{ $miembro['porcentaje'] >= 100 ? 'success' : ($miembro['porcentaje'] >= 75 ? 'info' : 'primary') }}" style="width: {{ $miembro['porcentaje'] }}%"></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted py-4">
                                                                    <i class="ri-government-line fs-1 mb-3"></i>
                                                                    <p class="mb-0">No hay datos de elecciones disponibles</p>
                                                                </td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Asistencia a Asambleas</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="attendanceChart"></div>
                                        <div class="d-flex align-items-center">
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center fs-13">
                                                    <p class="text-muted mb-1">Miembros Activos</p>
                                                </div>
                                                <div>
                                                    <h5 class="text-primary">{{ $asistenciasData['miembros_activos_porcentaje'] }}%</h5>
                                                </div>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center fs-13">
                                                    <p class="text-muted mb-1">Fundadores</p>
                                                </div>
                                                <div>
                                                    <h5 class="text-success">{{ $asistenciasData['fundadores_porcentaje'] }}%</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-3">
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center fs-13">
                                                    <p class="text-muted mb-1">Estudiantes</p>
                                                </div>
                                                <div>
                                                    <h5 class="text-info">{{ $asistenciasData['estudiantes_porcentaje'] }}%</h5>
                                                </div>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center fs-13">
                                                    <p class="text-muted mb-1">Total Miembros</p>
                                                </div>
                                                <div>
                                                    <h5 class="text-secondary">{{ number_format($estadisticas['total_miembros']) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card">
                                        <div class="card-header">
                                            <h4>Resumen Financiero</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                        <div class="card-body">
                                            <div class="w-50">
                                                <h1>${{ number_format($estadisticas['ingresos_mes'] - $estadisticas['gastos_mes'], 2) }}</h1>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span class="text-muted fs-18">Gastos del Mes <i class="ri-error-warning-fill"></i></span>
                                                        <h3 class="text-danger">-${{ number_format($estadisticas['gastos_mes'], 2) }}</h3>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted fs-18">Ingresos del Mes <i class="ri-check-line"></i></span>
                                                        <h3 class="text-success">${{ number_format($estadisticas['ingresos_mes'], 2) }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="income"></div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card overflow-hidden">
                                    <div class="card-body school-card">
                                        <div class="position-relative z-1 h-100">
                                            <h2 class="text-white">Join the cormmunity and find out more...</h2>
                                            <a href="javascript:void(0)" class="btn btn-light position-absolute bottom-0">Explore now</a>
                                        </div>
                                        <div>
                                            <img src="{{ asset('assets/images/dashboard/school-bg.png') }}" alt="School Background" class="position-absolute start-0 bottom-0 opacity-1 img-fluid">
                                            <img src="{{ asset('assets/images/dashboard/school-bg1.png') }}" alt="School Background" class="position-absolute opacity-1 img-fluid end-0 top-0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4>May 2023</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="datepicker-container">
                                            <input type="text" class="form-control d-none" id="inline-picker" placeholder="Select a date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4>Próximos Eventos</h4>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @forelse($proximosEventos as $evento)
                                        <div class="d-flex align-items-center">
                                            <div class="h-64px w-64px position-relative d-flex justify-content-center align-items-center bg-{{ $evento['color'] }} text-white fs-4 rounded-3 me-3">
                                                <div class="d-block">
                                                    <h3 class="mb-0 text-white text-center">{{ $evento['fecha']->format('d') }}</h3>
                                                    <p class="mb-0 fs-16">{{ $evento['fecha']->format('M') }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="fs-18 mb-0">{{ $evento['titulo'] }}</h6>
                                                <p class="mb-0">
                                                    <i class="ri-{{ $evento['tipo'] == 'asamblea' ? 'calendar-line' : 'government-line' }}"></i>
                                                    {{ $evento['lugar'] }}
                                                    @if($evento['modalidad'] == 'virtual')
                                                        <span class="badge bg-success ms-2">Virtual</span>
                                                    @elseif($evento['modalidad'] == 'presencial')
                                                        <span class="badge bg-primary ms-2">Presencial</span>
                                                    @else
                                                        <span class="badge bg-info ms-2">{{ ucfirst($evento['modalidad']) }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @if(!$loop->last)
                                        <hr class="my-5">
                                        @endif
                                        @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="ri-calendar-line fs-1 mb-3"></i>
                                            <p class="mb-0">No hay eventos programados</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Avisos Importantes</h4>
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="text-body" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('noticias.index') }}">Ver Todas las Noticias</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)">This Week</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)">This Month</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body card-h-100">
                                            @forelse($noticiasImportantes as $noticia)
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="{{ $noticia['icono'] }} text-{{ $noticia['color'] }} fs-5"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <h6 class="fs-18 mb-0 me-2">{{ $noticia['titulo'] }}</h6>
                                                        @if($noticia['modalidad'] == 'virtual')
                                                            <span class="badge bg-success">Virtual</span>
                                                        @elseif($noticia['modalidad'] == 'presencial')
                                                            <span class="badge bg-primary">Presencial</span>
                                                        @elseif($noticia['modalidad'] == 'oficial')
                                                            <span class="badge bg-warning">Oficial</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-1 text-muted fs-14">{{ $noticia['descripcion'] }}</p>
                                                    <p class="mb-0 fs-13">
                                                        <i class="ri-calendar-line me-1"></i>
                                                        {{ $noticia['fecha']->format('d M Y') }}
                                                        <span class="ms-2">
                                                            <i class="ri-map-pin-2-fill me-1"></i>
                                                            {{ $noticia['lugar'] }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                            <hr class="my-4">
                                            @endif
                                            @empty
                                            <div class="text-center text-muted py-4">
                                                <i class="ri-notification-line fs-1 mb-3"></i>
                                                <p class="mb-0">No hay noticias importantes</p>
                                            </div>
                                            @endforelse
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div><!--End container-fluid-->
        </main><!--End app-wrapper-->

@endsection

@section('js')
    <!-- Datepicker Js -->
    <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>

    <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- File js -->
    <script src="{{ asset('assets/js/dashboard/school.init.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection