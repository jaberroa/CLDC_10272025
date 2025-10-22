@extends('partials.layouts.master')

@section('title', 'Noticias CLDCI')
@section('title-sub', 'Noticias')
@section('pagetitle', 'Noticias y Avisos Importantes')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Noticias y Avisos Importantes</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Noticias</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('noticias.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    @foreach($estadisticas['por_categoria'] as $categoria => $count)
                                        <option value="{{ $categoria }}" {{ $categoriaFiltro == $categoria ? 'selected' : '' }}>
                                            {{ $categoria }} ({{ $count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option value="">Todos los tipos</option>
                                    @foreach($estadisticas['por_tipo'] as $tipo => $count)
                                        <option value="{{ $tipo }}" {{ $tipoFiltro == $tipo ? 'selected' : '' }}>
                                            {{ ucfirst($tipo) }} ({{ $count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri-search-line me-1"></i>Filtrar
                                </button>
                                <a href="{{ route('noticias.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-xxl-3 col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-primary-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-primary d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-notification-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Total Noticias</span>
                                <h4 class="mb-0 fw-semibold">{{ number_format($estadisticas['total_noticias']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-info-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-info d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-calendar-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Asambleas</span>
                                <h4 class="mb-0 fw-semibold">{{ $estadisticas['por_categoria']['Asambleas'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-warning-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-warning d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-megaphone-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Comunicados</span>
                                <h4 class="mb-0 fw-semibold">{{ $estadisticas['por_categoria']['Comunicados'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card overflow-hidden">
                    <div class="card-body bg-success-subtle position-relative z-1">
                        <div class="d-flex gap-2">
                            <div class="school-icon bg-success d-flex justify-content-center align-items-center fs-4">
                                <i class="ri-graduation-cap-line"></i>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-semibold mb-2 fs-5">Capacitaciones</span>
                                <h4 class="mb-0 fw-semibold">{{ $estadisticas['por_categoria']['Capacitaciones'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Noticias -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Todas las Noticias</h4>
                    </div>
                    <div class="card-body">
                        @forelse($noticias as $noticia)
                        <div class="d-flex align-items-start mb-4 p-3 border rounded-3">
                            <div class="h-64px w-64px position-relative d-flex justify-content-center align-items-center bg-{{ $noticia['color'] }} text-white fs-4 rounded-3 me-3 flex-shrink-0">
                                <i class="{{ $noticia['icono'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1">{{ $noticia['titulo'] }}</h5>
                                        <p class="text-muted mb-2">{{ $noticia['descripcion'] }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $noticia['color'] }}">{{ $noticia['categoria'] }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-muted fs-13">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span class="me-3">{{ $noticia['fecha']->format('d M Y, H:i') }}</span>
                                    <i class="ri-map-pin-2-fill me-1"></i>
                                    <span class="me-3">{{ $noticia['lugar'] }}</span>
                                    @if($noticia['modalidad'] == 'virtual')
                                        <span class="badge bg-success">Virtual</span>
                                    @elseif($noticia['modalidad'] == 'presencial')
                                        <span class="badge bg-primary">Presencial</span>
                                    @elseif($noticia['modalidad'] == 'oficial')
                                        <span class="badge bg-warning">Oficial</span>
                                    @elseif($noticia['modalidad'] == 'financiero')
                                        <span class="badge bg-success">Financiero</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="ri-notification-line fs-1 mb-3"></i>
                            <h5 class="mb-2">No hay noticias disponibles</h5>
                            <p class="mb-0">No se encontraron noticias con los filtros aplicados.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection

